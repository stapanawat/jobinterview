<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use Illuminate\Support\Facades\Log;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use GuzzleHttp\Client;

class PublicApplicationController extends Controller
{
    /**
     * Show the application form (LIFF page)
     */
    public function showForm()
    {
        $liffId = env('LIFF_ID');
        $reviews = \App\Models\Review::with('applicant')
            ->where('reviewer_type', 'employee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $avgRating = \App\Models\Review::where('reviewer_type', 'employee')->avg('rating');
        $totalReviews = \App\Models\Review::where('reviewer_type', 'employee')->count();

        $positions = \App\Models\Position::where('is_active', true)->get();

        return view('public.apply', compact('liffId', 'reviews', 'avgRating', 'totalReviews', 'positions'));
    }

    public function allReviews(Request $request)
    {
        $query = \App\Models\Review::with('applicant')
            ->where('reviewer_type', 'employee')
            ->orderBy('created_at', 'desc');

        if ($request->filled('position')) {
            $query->whereHas('applicant', function ($q) use ($request) {
                $q->where('position', $request->position);
            });
        }

        $reviews = $query->paginate(20)->onEachSide(0);

        $avgRating = \App\Models\Review::where('reviewer_type', 'employee')->avg('rating');
        $totalReviews = \App\Models\Review::where('reviewer_type', 'employee')->count();
        $positions = \App\Models\Position::where('is_active', true)->get();

        return view('public.reviews', compact('reviews', 'avgRating', 'totalReviews', 'positions'));
    }

    /**
     * Handle form submission
     */
    public function submitForm(Request $request)
    {
        $validatedData = $request->validate([
            'line_user_id' => 'required|string',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'position' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'experience' => 'nullable|string|max:1000',
            'id_card_image' => 'nullable|image|max:5120',
            'photo' => 'nullable|image|max:5120',
            'pdpa_accepted' => 'required|accepted',
            // New fields validation
            'current_residence' => 'nullable|string|max:255',
            'current_occupation' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:18|max:100',
            'education_level' => 'nullable|string|max:255',
            'number_of_children' => 'nullable|integer|min:0|max:20',
            'can_drive_motorcycle' => 'nullable|string|max:100',
            'pros_and_cons' => 'nullable|string|max:2000',
            'life_dream' => 'nullable|string|max:2000',
            'emergency_contact' => 'nullable|string|max:500',
            'preferred_working_hours' => 'nullable|string|max:500',
        ], [
            'name.required' => 'กรุณากรอกชื่อ-นามสกุล',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'position.required' => 'กรุณากรอกตำแหน่งที่ต้องการสมัคร',
            'pdpa_accepted.required' => 'กรุณายอมรับข้อตกลง PDPA',
            'pdpa_accepted.accepted' => 'กรุณายอมรับข้อตกลง PDPA',
            'id_card_image.max' => 'ไฟล์บัตรประชาชนต้องไม่เกิน 5MB',
            'photo.max' => 'ไฟล์รูปถ่ายต้องไม่เกิน 5MB',
        ]);

        // Prepare data for creation
        $applicantData = [
            'line_user_id' => $validatedData['line_user_id'],
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'position' => $validatedData['position'],
            'address' => $validatedData['address'] ?? null,
            'experience' => $validatedData['experience'] ?? null,
            'line_display_name' => $request->line_display_name, // Assuming these come from request directly, not validated
            'line_picture_url' => $request->line_picture_url,   // Assuming these come from request directly, not validated
            'pdpa_accepted' => true,
            'status' => 'pending_review',
            'current_residence' => $validatedData['current_residence'] ?? null,
            'current_occupation' => $validatedData['current_occupation'] ?? null,
            'age' => $validatedData['age'] ?? null,
            'education_level' => $validatedData['education_level'] ?? null,
            'number_of_children' => $validatedData['number_of_children'] ?? null,
            'can_drive_motorcycle' => $validatedData['can_drive_motorcycle'] ?? null,
            'pros_and_cons' => $validatedData['pros_and_cons'] ?? null,
            'life_dream' => $validatedData['life_dream'] ?? null,
            'emergency_contact' => $validatedData['emergency_contact'] ?? null,
            'preferred_working_hours' => $validatedData['preferred_working_hours'] ?? null,
        ];

        // Create a new applicant record for every submission
        $applicant = Applicant::create($applicantData);

        // Handle file uploads
        try {
            if ($request->hasFile('id_card_image')) {
                $path = $request->file('id_card_image')->store('id-cards', 'public');
                $applicant->id_card_image = $path;
            }

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('photos', 'public');
                $applicant->photo = $path;
            }

            $applicant->save(); // Save the applicant with image paths
        } catch (\Exception $e) {
            Log::warning('File upload failed: ' . $e->getMessage());
        }



        return response()->json(['success' => true, 'message' => 'สมัครงานสำเร็จ!']);
    }
}
