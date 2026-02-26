<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('applicant')->orderBy('created_at', 'desc');

        if ($request->filled('reviewer_type')) {
            $query->where('reviewer_type', $request->reviewer_type);
        }

        if ($request->filled('applicant_id')) {
            $query->where('applicant_id', $request->applicant_id);
        }

        $reviews = $query->get();
        $applicants = Applicant::whereNotNull('name')->orderBy('name')->get();

        return view('reviews.index', compact('reviews', 'applicants'));
    }

    public function create(Request $request)
    {
        $applicant = Applicant::findOrFail($request->applicant);
        return view('reviews.create', compact('applicant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'reviewer_type' => 'required|in:shop,employee',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'applicant_id' => $request->applicant_id,
            'reviewer_type' => $request->reviewer_type,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('reviews.index')->with('success', 'บันทึกรีวิวเรียบร้อยแล้ว!');
    }
}
