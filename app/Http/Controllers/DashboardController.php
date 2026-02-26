<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;

class DashboardController extends Controller
{
    public function index()
    {
        $applicants = Applicant::with('interviews')->orderBy('created_at', 'desc')->get();

        $stats = $this->getStats($applicants);

        return view('dashboard', compact('applicants', 'stats'));
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

    private function getStats($applicants)
    {
        return [
            'total' => $applicants->count(),
            'pending' => $applicants->where('status', 'pending_review')->count(),
            'scheduled' => $applicants->where('status', 'scheduled')->count(),
            'time_confirmed' => $applicants->where('status', 'time_confirmed')->count(),
            'attendance_confirmed' => $applicants->where('status', 'attendance_confirmed')->count(),
        ];
    }
}
