<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Interview;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\FlexMessage;
use LINE\Clients\MessagingApi\Model\FlexBubble;
use LINE\Clients\MessagingApi\Model\FlexBox;
use LINE\Clients\MessagingApi\Model\FlexText;
use LINE\Clients\MessagingApi\Model\FlexButton;
use LINE\Clients\MessagingApi\Model\PostbackAction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    public function create(Request $request)
    {
        $applicant = Applicant::findOrFail($request->applicant);
        return view('interviews.create', compact('applicant'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_id' => 'required|exists:applicants,id',
            'interview_date' => 'required|date',
            'interview_time' => 'required',
            'location' => 'required',
        ]);

        // Prevent duplicate scheduling
        $existing = Interview::where('applicant_id', $request->applicant_id)
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')->with('error', 'ผู้สมัครคนนี้มีนัดสัมภาษณ์อยู่แล้ว');
        }

        $interview = Interview::create([
            'applicant_id' => $request->applicant_id,
            'interview_date' => $request->interview_date,
            'interview_time' => $request->interview_time,
            'location' => $request->location,
        ]);

        $applicant = Applicant::find($request->applicant_id);
        $applicant->update(['status' => 'scheduled']);

        try {
            $this->sendFlexMessage($applicant, $interview);
            return redirect()->route('dashboard')->with('success', 'นัดสัมภาษณ์เรียบร้อยแล้ว และส่งข้อความทาง LINE แล้ว!');
        } catch (\Exception $e) {
            Log::error('LINE Push Error: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'บันทึกนัดสัมภาษณ์แล้ว แต่ส่งข้อความ LINE ไม่สำเร็จ: ' . substr($e->getMessage(), 0, 150));
        }
    }

    private function sendFlexMessage($applicant, $interview)
    {
        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        $dateFormatted = \Carbon\Carbon::parse($interview->interview_date)->format('d/m/Y');

        $flexBubble = new FlexBubble([
            'type' => 'bubble',
            'body' => new FlexBox([
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    new FlexText(['type' => 'text', 'text' => 'นัดหมายสัมภาษณ์งาน', 'weight' => 'bold', 'size' => 'xl']),
                    new FlexText(['type' => 'text', 'text' => "ถึงคุณ {$applicant->name}", 'margin' => 'md']),
                    new FlexText(['type' => 'text', 'text' => "📅 วันที่: {$dateFormatted}", 'margin' => 'sm']),
                    new FlexText(['type' => 'text', 'text' => "⏰ เวลา: {$interview->interview_time}", 'margin' => 'sm']),
                    new FlexText(['type' => 'text', 'text' => "📍 สถานที่: {$interview->location}", 'margin' => 'sm', 'wrap' => true]),
                ],
            ]),
            'footer' => new FlexBox([
                'type' => 'box',
                'layout' => 'horizontal',
                'spacing' => 'sm',
                'contents' => [
                    new FlexButton([
                        'type' => 'button',
                        'style' => 'primary',
                        'color' => '#28a745',
                        'action' => new PostbackAction([
                            'type' => 'postback',
                            'label' => 'ยืนยัน',
                            'data' => "action=confirm&interview_id={$interview->id}",
                        ])
                    ]),
                    new FlexButton([
                        'type' => 'button',
                        'style' => 'primary',
                        'color' => '#dc3545',
                        'action' => new PostbackAction([
                            'type' => 'postback',
                            'label' => 'ขอเลื่อน',
                            'data' => "action=reschedule&interview_id={$interview->id}",
                        ])
                    ]),
                    new FlexButton([
                        'type' => 'button',
                        'style' => 'link',
                        'color' => '#6c757d',
                        'action' => new PostbackAction([
                            'type' => 'postback',
                            'label' => 'ยกเลิกนัด',
                            'data' => "action=cancel&interview_id={$interview->id}",
                        ])
                    ])
                ]
            ])
        ]);

        $message = new FlexMessage([
            'type' => 'flex',
            'altText' => 'นัดหมายสัมภาษณ์งาน',
            'contents' => $flexBubble
        ]);

        $request = new PushMessageRequest([
            'to' => $applicant->line_user_id,
            'messages' => [$message]
        ]);

        try {
            $messagingApi->pushMessage($request);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send LINE interview invitation to {$applicant->name}: " . $e->getMessage());
            // We don't throw the error so the interview is still saved in DB even if LINE fails
        }
    }

    public function cancel($applicantId)
    {
        $applicant = Applicant::findOrFail($applicantId);

        $interview = $applicant->interviews()->whereNotIn('status', ['cancelled'])->latest()->first();
        if ($interview) {
            $interview->update(['status' => 'cancelled']);
        }

        $applicant->update(['status' => 'cancelled']);

        // Send cancellation message via LINE
        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        $message = new \LINE\Clients\MessagingApi\Model\TextMessage([
            'type' => 'text',
            'text' => "ขออภัยครับ ทาง HR ได้มีการยกเลิกการนัดหมายสัมภาษณ์ของคุณเรียบร้อยแล้ว หากมีข้อสงสัยเพิ่มเติม สามารถพิมพ์สอบถามทิ้งไว้ได้เลยครับ"
        ]);

        $request = new PushMessageRequest([
            'to' => $applicant->line_user_id,
            'messages' => [$message]
        ]);

        try {
            $messagingApi->pushMessage($request);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send cancellation to {$applicant->name}: " . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'ยกเลิกการนัดสัมภาษณ์เรียบร้อยแล้ว');
    }
}
