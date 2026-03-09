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
            ->limit(20)
            ->get();

        $avgRating = \App\Models\Review::where('reviewer_type', 'employee')->avg('rating');
        $totalReviews = \App\Models\Review::where('reviewer_type', 'employee')->count();

        $positions = \App\Models\Position::where('is_active', true)->get();

        return view('public.apply', compact('liffId', 'reviews', 'avgRating', 'totalReviews', 'positions'));
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

        // Handle file uploads
        $idCardPath = null;
        $photoPath = null;

        try {
            if ($request->hasFile('id_card_image')) {
                $idCardPath = $request->file('id_card_image')->store('id-cards', 'public');
            }

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('photos', 'public');
            }
        } catch (\Exception $e) {
            Log::warning('File upload failed: ' . $e->getMessage());
        }

        // Build update data (only include image fields if new file was uploaded)
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'position' => $request->position,
            'address' => $request->address,
            'experience' => $request->experience,
            'line_display_name' => $request->line_display_name,
            'line_picture_url' => $request->line_picture_url,
            'pdpa_accepted' => true,
            'status' => 'pending_review',
            'current_residence' => $request->current_residence,
            'current_occupation' => $request->current_occupation,
            'age' => $request->age,
            'education_level' => $request->education_level,
            'number_of_children' => $request->number_of_children,
            'can_drive_motorcycle' => $request->can_drive_motorcycle,
            'pros_and_cons' => $request->pros_and_cons,
            'life_dream' => $request->life_dream,
            'emergency_contact' => $request->emergency_contact,
            'preferred_working_hours' => $request->preferred_working_hours,
        ];

        // Only update image paths if new files were uploaded
        if ($idCardPath) {
            $updateData['id_card_image'] = $idCardPath;
        }
        if ($photoPath) {
            $updateData['photo'] = $photoPath;
        }

        // Create or update applicant
        $applicant = Applicant::updateOrCreate(
            ['line_user_id' => $request->line_user_id],
            $updateData
        );

        return response()->json(['success' => true, 'message' => 'สมัครงานสำเร็จ!']);
    }
}
