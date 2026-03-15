<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Review;
use App\Models\Position;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        // Only fetch reviews for applicants who are actual employees (working or terminated)
        $query = Review::whereHas('applicant', function ($q) {
            $q->whereIn('status', ['working', 'terminated']);
        })->with('applicant')->orderBy('created_at', 'desc');

        $applicant = null;
        if ($request->filled('applicant_id')) {
            $query->where('applicant_id', $request->applicant_id);
            $applicant = Applicant::find($request->applicant_id);
        }

        if ($request->filled('reviewer_type')) {
            $query->where('reviewer_type', $request->reviewer_type);
        }

        if ($request->filled('search')) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('position')) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('position', $request->position);
            });
        }

        if ($request->filled('date_filter')) {
            $dateFilter = $request->date_filter;
            $now = Carbon::now();

            if ($dateFilter === 'today') {
                $query->whereDate('created_at', $now->toDateString());
            } elseif ($dateFilter === 'yesterday') {
                $query->whereDate('created_at', $now->subDay()->toDateString());
            } elseif ($dateFilter === 'this_week') {
                $query->whereBetween('created_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
            } elseif ($dateFilter === 'this_month') {
                $query->whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year);
            }
        }

        $reviews = $query->paginate(20);
        $positions = Position::where('is_active', true)->orderBy('name')->get();

        return view('reviews.index', compact('reviews', 'positions', 'applicant'));
    }

    public function create(Request $request)
    {
        $applicant = Applicant::findOrFail($request->applicant);
        return view('reviews.create', compact('applicant'));
    }

    public function store(Request $request)
    {
        $rules = [
            'applicant_id' => 'required|exists:applicants,id',
            'reviewer_type' => 'required|in:shop,employee',
            'comment' => 'nullable|string|max:1000',
        ];

        if ($request->reviewer_type === 'employee') {
            $rules['rating'] = 'required|integer|min:1|max:5';
        } else {
            // For shop reviews
            $rules['rating_punctuality'] = 'required|integer|min:1|max:5';
            $rules['rating_showed_up'] = 'required|integer|min:1|max:5';
            $rules['rating_honesty'] = 'required|integer|min:1|max:5';
            $rules['rating_diligence'] = 'required|integer|min:1|max:5';
            $rules['rating_following_instructions'] = 'required|integer|min:1|max:5';
            $rules['rating_others'] = 'required|integer|min:1|max:5';
        }

        $request->validate($rules);

        $data = [
            'applicant_id' => $request->applicant_id,
            'reviewer_type' => $request->reviewer_type,
            'comment' => $request->comment,
        ];

        if ($request->reviewer_type === 'employee') {
            $data['rating'] = $request->rating;
        } else {
            $data['rating_punctuality'] = $request->rating_punctuality;
            $data['rating_showed_up'] = $request->rating_showed_up;
            $data['rating_honesty'] = $request->rating_honesty;
            $data['rating_diligence'] = $request->rating_diligence;
            $data['rating_following_instructions'] = $request->rating_following_instructions;
            $data['rating_others'] = $request->rating_others;

            // Calculate overall rating as average
            $total = $request->rating_punctuality + $request->rating_showed_up + $request->rating_honesty +
                $request->rating_diligence + $request->rating_following_instructions + $request->rating_others;
            $data['rating'] = (int) round($total / 6);
        }

        Review::create($data);

        return redirect()->route('reviews.index')->with('success', 'บันทึกรีวิวเรียบร้อยแล้ว!');
    }
}
