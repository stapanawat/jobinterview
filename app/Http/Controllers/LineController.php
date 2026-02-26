<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Applicant;
use App\Models\Review;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\HTTPHeader;
use GuzzleHttp\Client;

class LineController extends Controller
{
    private $messagingApi;
    private $channelSecret;

    public function __construct()
    {
        $this->channelSecret = env('LINE_BOT_CHANNEL_SECRET');
        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $this->messagingApi = new MessagingApiApi(
            client: $client,
            config: $config,
        );
    }

    public function handle(Request $request)
    {
        $signature = $request->header(HTTPHeader::LINE_SIGNATURE);
        if (empty($signature)) {
            return response('Bad Request', 400);
        }

        $events = $request->input('events', []);

        foreach ($events as $event) {
            if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
                $replyToken = $event['replyToken'];
                $text = trim($event['message']['text']);
                $userId = $event['source']['userId'];

                $this->handleMessage($replyToken, $text, $userId);
            } elseif ($event['type'] === 'follow') {
                $replyToken = $event['replyToken'];
                $this->sendTutorial($replyToken);
            } elseif ($event['type'] === 'postback') {
                $replyToken = $event['replyToken'];
                $data = $event['postback']['data'];
                $this->handlePostback($replyToken, $data);
            }
        }

        return response('OK', 200);
    }

    private function handlePostback($replyToken, $data)
    {
        parse_str($data, $params);
        $action = $params['action'] ?? null;
        $interviewId = $params['interview_id'] ?? null;

        if ($action && $interviewId) {
            $interview = \App\Models\Interview::find($interviewId);
            if ($interview) {
                // Prevent duplicate clicks
                if ($action === 'confirm' && in_array($interview->status, ['time_confirmed', 'attendance_confirmed', 'reschedule_requested'])) {
                    $this->replyText($replyToken, "คุณได้ตอบกลับการนัดหมายนี้แล้วครับ");
                    return;
                }

                if ($action === 'confirm') {
                    $interview->update(['status' => 'time_confirmed']);
                    $interview->applicant->update(['status' => 'time_confirmed']);
                    $this->replyText($replyToken, "ขอบคุณครับ ยืนยันเวลานัดสัมภาษณ์เรียบร้อยแล้ว แล้วพบกันครับ!");
                } elseif ($action === 'reschedule') {
                    $interview->update(['status' => 'reschedule_requested']);
                    $interview->applicant->update(['status' => 'pending_review']);
                    $this->replyText($replyToken, "รับทราบครับ ทาง HR จะติดต่อกลับเพื่อทำการนัดหมายเวลาใหม่อีกครั้งครับ");
                } elseif ($action === 'day_confirm') {
                    if ($interview->day_before_confirmed || $interview->status === 'attendance_confirmed') {
                        $this->replyText($replyToken, "คุณได้ยืนยันแล้วครับ ขอบคุณครับ! 🙏");
                        return;
                    }
                    $interview->update(['day_before_confirmed' => true, 'status' => 'attendance_confirmed']);
                    $interview->applicant->update(['status' => 'attendance_confirmed']);
                    $this->replyText($replyToken, "✅ ยืนยันเรียบร้อย! ขอบคุณครับ\n\nแล้วพบกันพรุ่งนี้เวลา {$interview->interview_time}\n📍 {$interview->location}\n\nขอให้โชคดีครับ! 🙏");
                } elseif ($action === 'day_cancel') {
                    $interview->update(['status' => 'cancelled']);
                    $interview->applicant->update(['status' => 'cancelled']);
                    $this->replyText($replyToken, "รับทราบครับ ได้ยกเลิกนัดหมายแล้ว\nหากต้องการสมัครอีกครั้ง พิมพ์ 'สมัครงาน' ได้เลยครับ");
                }
            } else {
                $this->replyText($replyToken, "ไม่พบข้อมูลการนัดหมายนี้");
            }
        }
    }

    private function handleMessage($replyToken, $text, $userId)
    {
        $applicant = Applicant::where('line_user_id', $userId)->first();

        // Check if user is in the review flow
        $reviewState = Cache::get("review_state_{$userId}");

        // --- Help / Tutorial ---
        if ($text === 'วิธีใช้งาน' || $text === 'สอนการใช้งาน' || $text === 'help' || $text === 'ช่วยเหลือ') {
            $this->sendTutorial($replyToken);
            return;
        }

        // --- View Reviews ---
        if ($text === 'ดูรีวิว') {
            // Get all reviews for the shop (from any employee)
            $allEmployeeReviews = Review::where('reviewer_type', 'employee')->get();
            $recentReviews = Review::where('reviewer_type', 'employee')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            if ($allEmployeeReviews->isEmpty()) {
                $this->replyText($replyToken, "📋 ยังไม่มีรีวิวร้านค้าในขณะนี้ครับ");
                return;
            }

            $avgRating = round($allEmployeeReviews->avg('rating'), 1);
            $totalCount = $allEmployeeReviews->count();
            $stars = str_repeat('⭐', floor($avgRating));

            $msg = "📋 รีวิวร้านค้าจากพนักงานทั้งหมด\n";
            $msg .= "คะแนนเฉลี่ย: {$avgRating} / 5 {$stars} ({$totalCount} รีวิว)\n";
            $msg .= "------------------------\n";

            foreach ($recentReviews as $i => $review) {
                $rStars = str_repeat('⭐', $review->rating);
                $date = $review->created_at->format('d/m/Y');
                $comment = $review->comment ?: '-';
                $name = $review->applicant ? substr($review->applicant->name, 0, 1) . '***' : 'ผู้สมัครงาน';
                $msg .= "\n" . ($i + 1) . ". {$name}\n   {$rStars}\n   \"{$comment}\"\n   📅 {$date}\n";
            }

            if ($totalCount > 5) {
                $msg .= "\n(แสดง 5 รายการล่าสุดจากทั้งหมด {$totalCount} รายการ)";
            }

            $this->replyText($replyToken, $msg);
            return;
        }

        // --- Review Flow ---
        if ($text === 'รีวิวร้านค้า') {
            if (!$applicant || empty($applicant->name)) {
                $this->replyText($replyToken, "กรุณาสมัครงานก่อนจึงจะรีวิวได้ครับ\nพิมพ์ 'สมัครงาน' เพื่อเริ่มต้น");
                return;
            }
            Cache::put("review_state_{$userId}", 'awaiting_rating', 600);
            $this->replyText($replyToken, "📝 รีวิวร้านค้า\n\nกรุณาให้คะแนน 1-5 ครับ\n⭐ 1 = แย่มาก\n⭐⭐ 2 = แย่\n⭐⭐⭐ 3 = ปานกลาง\n⭐⭐⭐⭐ 4 = ดี\n⭐⭐⭐⭐⭐ 5 = ดีมาก");
            return;
        }

        if ($reviewState === 'awaiting_rating') {
            $rating = intval($text);
            if ($rating < 1 || $rating > 5) {
                $this->replyText($replyToken, "กรุณาพิมพ์ตัวเลข 1-5 เท่านั้นครับ");
                return;
            }
            Cache::put("review_state_{$userId}", 'awaiting_comment', 600);
            Cache::put("review_rating_{$userId}", $rating, 600);
            $this->replyText($replyToken, "ได้รับคะแนน {$rating} ⭐ แล้วครับ\n\nกรุณาพิมพ์ความคิดเห็นเพิ่มเติม หรือพิมพ์ 'ข้าม' ถ้าไม่ต้องการเขียนครับ");
            return;
        }

        if ($reviewState === 'awaiting_comment') {
            $rating = Cache::get("review_rating_{$userId}");
            $comment = ($text === 'ข้าม') ? null : $text;

            Review::create([
                'applicant_id' => $applicant->id,
                'reviewer_type' => 'employee',
                'rating' => $rating,
                'comment' => $comment,
            ]);

            // Clear cache
            Cache::forget("review_state_{$userId}");
            Cache::forget("review_rating_{$userId}");

            $stars = str_repeat('⭐', $rating);
            $now = now()->format('n/j/Y H:i:s');
            $summary = "บันทึกรีวิวเรียบร้อย!\n\nชื่อ: {$applicant->name}\nคะแนน: {$stars}\nความคิดเห็น: " . ($comment ?: '-') . "\nเวลา: {$now}\n\nขอบคุณสำหรับรีวิวครับ 🙏";
            $this->replyText($replyToken, $summary);
            return;
        }

        // --- Application Flow ---
        if ($text === 'สมัครงาน') {
            $liffId = env('LIFF_ID');
            $liffUrl = "https://liff.line.me/{$liffId}";

            if ($applicant && !empty($applicant->name) && !empty($applicant->position)) {
                $this->replyText($replyToken, "ข้อมูลของคุณอยู่ในระบบแล้วครับ รอการติดต่อจาก HR นะครับ\n\nหากต้องการสมัครใหม่ กดลิงก์ด้านล่างได้เลย:\n{$liffUrl}\n\nพิมพ์ 'รีวิวร้านค้า' เพื่อรีวิวร้านค้า\nพิมพ์ 'ดูรีวิว' เพื่อดูรีวิวของร้าน\nพิมพ์ 'วิธีใช้งาน' เพื่อดูคำแนะนำ");
            } else {
                $this->replyText($replyToken, "ยินดีต้อนรับสู่ระบบสมัครงานครับ 🎉\n\nกดลิงก์ด้านล่างเพื่อกรอกใบสมัคร:\n{$liffUrl}\n\nสะดวก รวดเร็ว กรอกข้อมูลครบจบในหน้าเดียว! 📝");
            }
            return;
        }

        if ($applicant && !empty($applicant->name)) {
            $this->replyText($replyToken, "ข้อมูลของคุณอยู่ในระบบแล้ว รอการติดต่อจาก HR นะครับ\n\nพิมพ์ 'รีวิวร้านค้า' เพื่อรีวิวร้านค้า\nพิมพ์ 'ดูรีวิว' เพื่อดูรีวิวของคุณ\nพิมพ์ 'วิธีใช้งาน' เพื่อดูคำแนะนำอีกครั้งครับ");
        }
    }

    private function sendTutorial($replyToken)
    {
        $liffId = env('LIFF_ID');
        $liffUrl = "https://liff.line.me/{$liffId}";

        $tutorial = "👋 ยินดีต้อนรับสู่ระบบ LINE Job!\n\n" .
            "ผมเป็นระบบอัตโนมัติที่จะช่วยจัดการเรื่องการสมัครงานและการนัดหมายของคุณครับ\n\n" .
            "✨ สิ่งที่คุณสามารถทำได้:\n\n" .
            "1️⃣ พิมพ์ 'สมัครงาน' — รับลิงก์ฟอร์มสมัครงานออนไลน์ กรอกครบจบในหน้าเดียว\n" .
            "2️⃣ พิมพ์ 'ดูรีวิว' — ดูรีวิวร้านค้าจากพนักงานคนอื่นๆ\n" .
            "3️⃣ พิมพ์ 'รีวิวร้านค้า' — แบ่งปันประสบการณ์การทำงาน\n" .
            "4️⃣ พิมพ์ 'วิธีใช้งาน' — เรียกดูคำแนะนำนี้ได้ทุกเมื่อ\n\n" .
            "📍 เมื่อมีการนัดสัมภาษณ์ ระบบจะส่งข้อความหาคุณเพื่อให้กดยืนยันหรือขอเลื่อนได้ทันที\n" .
            "📍 ล่วงหน้า 1 วัน ระบบจะถามยืนยันว่ามาแน่นอนหรือไม่\n\n" .
            "🔗 สมัครงานเลย: {$liffUrl}\n\n" .
            "ขอให้โชคดีกับการหางานนะครับ! 🙏";

        $this->replyText($replyToken, $tutorial);
    }

    private function replyText($replyToken, $text)
    {
        $message = new TextMessage(['type' => 'text', 'text' => $text]);
        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);

        try {
            $this->messagingApi->replyMessage($request);
        } catch (\Exception $e) {
            Log::error('LINE Reply Error: ' . $e->getMessage());
        }
    }
}
