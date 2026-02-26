<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Log;

class PublicApplicationController extends Controller
{
    /**
     * Show the application form (LIFF page)
     */
    public function showForm()
    {
        $liffId = env('LIFF_ID');
        $reviews = \App\Models\Review::where('reviewer_type', 'employee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $avgRating = \App\Models\Review::where('reviewer_type', 'employee')->avg('rating');
        $totalReviews = \App\Models\Review::where('reviewer_type', 'employee')->count();

        return view('public.apply', compact('liffId', 'reviews', 'avgRating', 'totalReviews'));
    }

    /**
     * Handle form submission
     */
    public function submitForm(Request $request)
    {
        $request->validate([
            'line_user_id' => 'required|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'experience' => 'nullable|string|max:1000',
            'id_card_image' => 'nullable|image|max:5120',
            'photo' => 'nullable|image|max:5120',
            'pdpa_accepted' => 'required|accepted',
        ], [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'position.required' => 'กรุณากรอกตำแหน่งที่ต้องการสมัคร',
            'pdpa_accepted.required' => 'กรุณายอมรับข้อตกลง PDPA',
            'pdpa_accepted.accepted' => 'กรุณายอมรับข้อตกลง PDPA',
            'id_card_image.max' => 'ไฟล์บัตรประชาชนต้องไม่เกิน 5MB',
            'photo.max' => 'ไฟล์รูปถ่ายต้องไม่เกิน 5MB',
        ]);

        // Handle file uploads
        $idCardPath = null;
        $photoPath = null;

        if ($request->hasFile('id_card_image')) {
            $idCardPath = $request->file('id_card_image')->store('id-cards', 'public');
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        // Create or update applicant
        $applicant = Applicant::updateOrCreate(
            ['line_user_id' => $request->line_user_id],
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'position' => $request->position,
                'address' => $request->address,
                'experience' => $request->experience,
                'id_card_image' => $idCardPath,
                'photo' => $photoPath,
                'line_display_name' => $request->line_display_name,
                'line_picture_url' => $request->line_picture_url,
                'pdpa_accepted' => true,
                'status' => 'pending_review',
            ]
        );

        Log::info("New web application from: {$applicant->name} (LINE: {$applicant->line_user_id})");

        return response()->json(['success' => true, 'message' => 'สมัครงานสำเร็จ!']);
    }
}
