<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Carbon\Carbon;
use App\Exports\EmployeesExport;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Applicant::with('interviews')->orderBy('updated_at', 'desc');

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Latest Only Filter
        if ($request->boolean('latest_only')) {
            // Group by line_user_id and select newest ID
            $latestIds = Applicant::selectRaw('MAX(id) as id')
                ->whereNotNull('line_user_id')
                ->groupBy('line_user_id')
                ->pluck('id');
            
            // For applicants without line_user_id, we might still want to group by phone
            $latestPhoneIds = Applicant::selectRaw('MAX(id) as id')
                ->whereNull('line_user_id')
                ->groupBy('phone')
                ->pluck('id');

            $query->whereIn('id', $latestIds->concat($latestPhoneIds));
        }

        $dateFilter = $request->input('date_filter');
        if ($dateFilter) {
            $now = Carbon::now();

            if ($dateFilter === 'today') {
                $query->whereDate('updated_at', $now->toDateString());
            } elseif ($dateFilter === 'yesterday') {
                $query->whereDate('updated_at', $now->subDay()->toDateString());
            } elseif ($dateFilter === 'this_week') {
                $query->whereBetween('updated_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
            } elseif ($dateFilter === 'this_month') {
                $query->whereMonth('updated_at', $now->month)
                    ->whereYear('updated_at', $now->year);
            }
        }

        $allApplicants = $query->get();
        $positions = \App\Models\Position::all();

        $stats = $this->getStats($allApplicants);

        // Split into interview process vs employees
        $applicants = $allApplicants->whereNotIn('status', ['working', 'terminated']);
        $employees = $allApplicants->whereIn('status', ['working', 'terminated']);

        return view('dashboard', compact('applicants', 'employees', 'stats', 'positions', 'dateFilter'));
    }

    public function history(Applicant $applicant)
    {
        $query = Applicant::orderBy('created_at', 'desc');
        
        if ($applicant->line_user_id) {
            $query->where('line_user_id', $applicant->line_user_id);
        } else {
            $query->where('phone', $applicant->phone);
        }

        $history = $query->get();
        return view('applicants.history', compact('history', 'applicant'));
    }

    public function updates()
    {
        $applicants = Applicant::with('interviews')->orderBy('created_at', 'desc')->get();
        $stats = $this->getStats($applicants);

        return response()->json([
            'html' => view('partials.applicants-table', compact('applicants'))->render(),
            'stats' => $stats,
        ]);
    }

    public function updateStatus(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending_review,scheduled,time_confirmed,attendance_confirmed,working,terminated,cancelled',
            'job_description' => 'nullable|string|max:1000',
        ]);

        $applicant->status = $validated['status'];

        if ($validated['status'] === 'working') {
            $applicant->job_description = $validated['job_description'] ?? $applicant->job_description;
        }

        $applicant->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'อัปเดตสถานะเรียบร้อยแล้ว']);
        }

        return redirect()->route('dashboard')->with('success', 'อัปเดตสถานะเรียบร้อยแล้ว');
    }

    public function updateNotes(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $applicant->notes = $validated['notes'];
        $applicant->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'บันทึกหมายเหตุเรียบร้อยแล้ว']);
        }

        return back()->with('success', 'บันทึกหมายเหตุเรียบร้อยแล้ว');
    }

    private function getStats($applicants)
    {
        return [
            'total' => $applicants->count(),
            'pending' => $applicants->where('status', 'pending_review')->count(),
            'scheduled' => $applicants->where('status', 'scheduled')->count(),
            'time_confirmed' => $applicants->where('status', 'time_confirmed')->count(),
            'attendance_confirmed' => $applicants->where('status', 'attendance_confirmed')->count(),
            'working' => $applicants->where('status', 'working')->count(),
            'terminated' => $applicants->where('status', 'terminated')->count(),
            'active_positions' => \App\Models\Position::where('is_active', true)->count(),
            'total_reviews' => \App\Models\Review::count(),
        ];
    }

    public function exportEmployees()
    {
        return Excel::download(new EmployeesExport, 'employees_' . date('Ymd') . '.xlsx');
    }
}

