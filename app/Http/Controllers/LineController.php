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
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\QuickReplyItem;
use LINE\Clients\MessagingApi\Model\MessageAction;
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
            Log::warning('LINE Webhook: Invalid or missing Signature.');
            return response('Bad Request', 400);
        }

        $events = $request->input('events', []);
        Log::info('LINE Webhook Payload: ', $events);


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
        Log::info('LINE Postback Data before parsing: ' . $data);
        parse_str($data, $params);
        Log::info('LINE Postback Parsed Params: ', $params);

        $action = $params['action'] ?? null;
        $interviewId = $params['interview_id'] ?? null;

        if ($action && $interviewId) {
            $interview = \App\Models\Interview::find($interviewId);
            if ($interview) {
                Log::info("Found Interview: {$interviewId}, Action: {$action}");
                // Prevent duplicate clicks
                $finalStatuses = ['time_confirmed', 'attendance_confirmed', 'reschedule_requested', 'cancelled'];
                if (in_array($action, ['confirm', 'reschedule', 'cancel']) && in_array($interview->status, $finalStatuses)) {
                    $this->replyText($replyToken, "คุณได้ตอบกลับการนัดหมายนี้ไปแล้วครับ");
                    return;
                }

                if ($action === 'confirm') {
                    $interview->update(['status' => 'time_confirmed']);
                    $interview->applicant->update(['status' => 'time_confirmed']);
                    $this->replyText($replyToken, "ขอบคุณครับ ยืนยันเวลานัดหมายเรียบร้อยแล้ว แล้วพบกันครับ!");
                } elseif ($action === 'reschedule') {
                    $interview->update(['status' => 'reschedule_requested']);
                    $interview->applicant->update(['status' => 'pending_review']);
                    $this->replyText($replyToken, "รับทราบครับ ทาง HR จะติดต่อกลับเพื่อทำการนัดหมายเวลาใหม่อีกครั้งครับ");
                } elseif ($action === 'cancel') {
                    $interview->update(['status' => 'cancelled']);
                    $interview->applicant->update(['status' => 'cancelled']);
                    $this->replyText($replyToken, "รับทราบครับ ได้ยกเลิกการนัดหมายเรียบร้อยแล้ว\nหากเปลี่ยนใจสามารถติดต่อ HR หรือพิมพ์ 'สมัครงาน' เพื่อเริ่มต้นใหม่ได้ครับ");
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
        $applicant = Applicant::where('line_user_id', $userId)->orderBy('created_at', 'desc')->first();

        // Check if user is in the review flow
        $reviewState = Cache::get("review_state_{$userId}");

        // --- Help / Tutorial ---
        if ($text === 'วิธีใช้งาน' || $text === 'สอนการใช้งาน' || $text === 'help' || $text === 'ช่วยเหลือ') {
            $this->sendTutorial($replyToken);
            return;
        }

        // --- Review Menu ---
        if ($text === 'รีวิว') {
            $positions = \App\Models\Position::where('is_active', true)->pluck('name')->toArray();
            if (empty($positions)) {
                $this->replyText($replyToken, "ขณะนี้ยังไม่มีตำแหน่งงานเปิดรับครับ");
                return;
            }

            $msg = "กรุณาเลือกตำแหน่งงานที่ต้องการรีวิวหรือดูรายละเอียดครับ:";
            $quickReplyItems = [];
            foreach (array_slice($positions, 0, 13) as $posName) {
                $label = mb_strlen($posName) > 20 ? mb_substr($posName, 0, 17) . '...' : $posName;
                $quickReplyItems[] = new QuickReplyItem([
                    'type' => 'action',
                    'action' => new MessageAction([
                        'type' => 'message',
                        'label' => $label,
                        'text' => "เลือกตำแหน่ง {$posName}", // Consistency
                    ]),
                ]);
            }

            $this->replyWithQuickReply($replyToken, $msg, $quickReplyItems);
            return;
        }

        if (str_starts_with($text, 'เลือกตำแหน่ง ')) {
            $posName = trim(mb_substr($text, mb_strlen('เลือกตำแหน่ง ')));
            $msg = "📍 ตำแหน่ง: {$posName}\nเลือกสิ่งที่คุณต้องการทำ:";
            $quickReplyItems = [
                new QuickReplyItem([
                    'type' => 'action',
                    'action' => new MessageAction([
                        'type' => 'message',
                        'label' => '✍️ เขียนรีวิวงาน',
                        'text' => "เริ่มเขียนรีวิว {$posName}",
                    ]),
                ]),
                new QuickReplyItem([
                    'type' => 'action',
                    'action' => new MessageAction([
                        'type' => 'message',
                        'label' => '⭐ ดูรีวิวงาน',
                        'text' => "ดูรีวิว {$posName}",
                    ]),
                ]),
                new QuickReplyItem([
                    'type' => 'action',
                    'action' => new MessageAction([
                        'type' => 'message',
                        'label' => '👤 ดูคะแนนพนักงาน',
                        'text' => "รีวิวพนักงาน {$posName}",
                    ]),
                ]),
            ];
            $this->replyWithQuickReply($replyToken, $msg, $quickReplyItems);
            return;
        }

        // --- Start Writing Review ---
        if (str_starts_with($text, 'เริ่มเขียนรีวิว ')) {
            $posName = trim(mb_substr($text, mb_strlen('เริ่มเขียนรีวิว ')));
            if (!$applicant || empty($applicant->name)) {
                $this->replyText($replyToken, "กรุณาสมัครงานก่อนจึงจะรีวิวได้ครับ\nพิมพ์ 'สมัครงาน' เพื่อเริ่มต้น");
                return;
            }
            // Find the SPECIFIC applicant record for this position to avoid mismatch
            $targetApplicant = Applicant::where('line_user_id', $userId)->where('position', $posName)->first();
            if (!$targetApplicant) {
                $targetApplicant = $applicant; // Fallback
            }
            Cache::put("review_applicant_id_{$userId}", $targetApplicant->id, 600);
            
            Cache::put("review_state_{$userId}", 'awaiting_rating', 600);
            Cache::put("review_position_{$userId}", $posName, 600);
            $this->replyText($replyToken, "📝 เขียนรีวิวงาน (ตำแหน่ง: {$posName})\n\nกรุณาให้คะแนน 1-5 ครับ\n⭐ 1 = แย่มาก\n⭐⭐ 2 = แย่\n⭐⭐⭐ 3 = ปานกลาง\n⭐⭐⭐⭐ 4 = ดี\n⭐⭐⭐⭐⭐ 5 = ดีมาก");
            return;
        }

        // --- View Shop Reviews (Stars only) ---
        if ($text === 'ดูรีวิว' || str_starts_with($text, 'ดูรีวิว ')) {
            $positionFilter = null;
            if (str_starts_with($text, 'ดูรีวิว ') && $text !== 'ดูรีวิว') {
                $positionFilter = trim(mb_substr($text, mb_strlen('ดูรีวิว ')));
            }

            $query = Review::with('applicant')->where('reviewer_type', 'employee');
            if ($positionFilter) {
                $query->whereHas('applicant', function ($q) use ($positionFilter) {
                    $q->where('position', $positionFilter);
                });
            }

            $allReviews = $query->orderBy('created_at', 'desc')->get();
            if ($allReviews->isEmpty()) {
                $msg = $positionFilter ? "📋 ยังไม่มีรีวิวสำหรับตำแหน่ง \"{$positionFilter}\" ครับ" : "📋 ยังไม่มีรีวิวร้านค้าในขณะนี้ครับ";
                $this->replyText($replyToken, $msg);
                return;
            }

            $avgRating = round($allReviews->avg('rating'), 1);
            $totalCount = $allReviews->count();
            $stars = str_repeat('⭐', floor($avgRating));
            
            $title = $positionFilter ? "📋 ดูรีวิวงาน (ตำแหน่ง: {$positionFilter})" : "📋 ดูรีวิวงานจากพนักงานทั้งหมด";
            $msg = "{$title}\n\nคะแนนเฉลี่ย: {$avgRating} / 5 {$stars}\nทั้งหมด {$totalCount} รีวิว\n";
            $msg .= "------------------------\n";
            $msg .= "แสดงเฉพาะคะแนนดาว:\n";
            $recentReviews = $allReviews->take(10);
            foreach ($recentReviews as $i => $review) {
                $rStars = str_repeat('⭐', $review->rating);
                $date = $review->created_at->format('d/m/Y');
                $pos = $review->applicant ? $review->applicant->position : '-';
                $name = $review->applicant ? $review->applicant->name : 'ไม่ระบุชื่อ';
                $msg .= "\n" . ($i + 1) . ". ผู้รีวิว: {$name}\n   [{$pos}] {$rStars}\n   📅 {$date}\n";
            }

            if ($totalCount > 10) {
                $msg .= "\n\n(แสดง 10 รายการล่าสุด)";
            }

            $this->replyText($replyToken, $msg);
            return;
        }

        // --- View Employee Reviews (From Shop) ---
        if (str_starts_with($text, 'รีวิวพนักงาน')) {
            $positionFilter = null;
            if (str_starts_with($text, 'รีวิวพนักงาน ') && $text !== 'รีวิวพนักงาน') {
                $positionFilter = trim(mb_substr($text, mb_strlen('รีวิวพนักงาน ')));
            }

            // User specifically wants to see names "everyone the shop reviewed"
            // So we show general reviews from shop to employees, including names.
            $query = Review::with('applicant')->where('reviewer_type', 'shop');
            if ($positionFilter) {
                $query->whereHas('applicant', function ($q) use ($positionFilter) {
                    $q->where('position', $positionFilter);
                });
            }

            $allReviews = $query->orderBy('created_at', 'desc')->take(10)->get();

            if ($allReviews->isEmpty()) {
                $msg = $positionFilter ? "📋 ไม่พบรีวิวพนักงานในตำแหน่ง \"{$positionFilter}\" ครับ" : "📋 ยังไม่มีรีวิวพนักงานในขณะนี้ครับ";
                $this->replyText($replyToken, $msg);
                return;
            }

            $msg = "👤 ดูคะแนนพนักงาน (รีวิวจากทางร้าน):\n";
            foreach ($allReviews as $i => $review) {
                $rStars = str_repeat('⭐', $review->rating);
                $date = $review->created_at->format('d/m/Y');
                $name = $review->applicant ? $review->applicant->name : 'พนักงาน';
                $pos = $review->applicant ? $review->applicant->position : '-';
                $msg .= "\n" . ($i + 1) . ". พนักงาน: {$name}\n   ตําแหน่ง: {$pos}\n   คะแนน: {$rStars}\n   📅 {$date}\n";
            }
            $this->replyText($replyToken, $msg);
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
            $posName = Cache::get("review_position_{$userId}");
            $comment = ($text === 'ข้าม') ? null : $text;

            $review = new Review();
            $review->applicant_id = Cache::get("review_applicant_id_{$userId}") ?? ($applicant ? $applicant->id : null);
            $review->reviewer_type = 'employee';
            $review->rating = $rating;
            $review->comment = $comment;
            
            if ($review->applicant_id) {
                $review->save();
                Log::info("Review saved for applicant ID: {$review->applicant_id}");
            } else {
                Log::warning("Could not save review: No applicant ID found for user {$userId}");
            }

            // Clear cache
            Cache::forget("review_state_{$userId}");
            Cache::forget("review_rating_{$userId}");
            Cache::forget("review_position_{$userId}");
            Cache::forget("review_applicant_id_{$userId}");

            $stars = str_repeat('⭐', $rating);
            $now = now()->format('d/m/Y H:i:s');
            $summary = "บันทึกรีวิวเรียบร้อย!\n\nตำแหน่งที่คุณรีวิว: {$posName}\nชื่อ: " . ($applicant ? $applicant->name : 'ผู้สมัครงาน') . "\nคะแนน: {$stars}\n\n(ความคิดเห็นของคุณถูกส่งเป็น Feedback ถึงบริษัทเรียบร้อยแล้วครับ)\n\nเวลา: {$now}\n\nขอบคุณสำหรับรีวิวครับ 🙏";
            $this->replyText($replyToken, $summary);
            return;
        }

        // --- Application Flow ---
        if ($text === 'สมัครงาน') {
            $liffId = env('LIFF_ID');
            $liffUrl = "https://liff.line.me/{$liffId}";

            if ($applicant && !empty($applicant->name) && !empty($applicant->position)) {
                $this->replyText($replyToken, "ข้อมูลของคุณอยู่ในระบบแล้วครับ รอการติดต่อจาก HR นะครับ\n\nหากต้องการสมัครใหม่ กดลิงก์ด้านล่างได้เลย:\n{$liffUrl}\n\nพิมพ์ 'รีวิว' เพื่อเข้าสู่เมนูรีวิว\nพิมพ์ 'วิธีใช้งาน' เพื่อดูคำแนะนำ");
            } else {
                $this->replyText($replyToken, "ยินดีต้อนรับสู่ระบบสมัครงานครับ 🎉\n\nกดลิงก์ด้านล่างเพื่อกรอกใบสมัคร:\n{$liffUrl}\n\nสะดวก รวดเร็ว กรอกข้อมูลครบจบในหน้าเดียว! 📝");
            }
            return;
        }

        if ($text === 'ยืนยันการส่งใบสมัคร') {
            if ($applicant && !empty($applicant->name)) {
                $time = $applicant->updated_at->timezone('Asia/Bangkok')->format('d/m/Y H:i:s');
                $replyText = "มีผู้สมัครใหม่!\n\nชื่อ: {$applicant->name}\nเบอร์: {$applicant->phone}\nตำแหน่ง: {$applicant->position}\nเวลา: {$time}\n\n\"🎉 สมัครงานสำเร็จครับ! ทางเราได้รับข้อมูลของคุณ {$applicant->name} สมัครตำแหน่ง {$applicant->position} เรียบร้อยแล้ว โปรดรอการติดต่อกลับจากทีม HR เพื่อร่วมนัดหมายในขั้นตอนต่อไปครับ หากต้องการสอบถามเพิ่มเติม สามารถพิมพ์ข้อความทิ้งไว้ได้เลยครับ\"";
                $this->replyText($replyToken, $replyText);
            }
            return;
        }

        if ($applicant && !empty($applicant->name)) {
            $this->replyText($replyToken, "ข้อมูลของคุณอยู่ในระบบแล้ว รอการติดต่อจาก HR นะครับ\n\nพิมพ์ 'รีวิว' เพื่อเข้าสู่เมนูรีวิว\nพิมพ์ 'วิธีใช้งาน' เพื่อดูคำแนะนำอีกครั้งครับ");
        }
    }

    private function sendTutorial($replyToken)
    {
        $liffId = env('LIFF_ID');
        $liffUrl = "https://liff.line.me/{$liffId}";

        $tutorial = "👋 ยินดีต้อนรับสู่ระบบ HR PKB!\n\n" .
            "ผมเป็นระบบอัตโนมัติที่จะช่วยจัดการเรื่องการสมัครงานและการนัดหมายของคุณครับ\n\n" .
            "✨ สิ่งที่คุณสามารถทำได้:\n\n" .
            "1️⃣ พิมพ์ 'สมัครงาน' — รับลิงก์ฟอร์มสมัครงานออนไลน์\n" .
            "2️⃣ พิมพ์ 'รีวิว' — เข้าสู่เมนู เขียนรีวิวงาน/ดูรีวิวงาน/ดูคะแนนพนักงาน\n" .
            "3️⃣ พิมพ์ 'วิธีใช้งาน' — เรียกดูคำแนะนำนี้ได้ทุกเมื่อ\n\n" .
            "📍 เมื่อมีการนัดหมาย ระบบจะส่งข้อความหาคุณเพื่อให้กดยืนยันหรือขอเลื่อนได้ทันที\n" .
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

    private function replyWithQuickReply($replyToken, $text, array $quickReplyItems)
    {
        $quickReply = new QuickReply(['items' => $quickReplyItems]);
        $message = new TextMessage([
            'type' => 'text',
            'text' => $text,
            'quickReply' => $quickReply,
        ]);
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
