<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Interview;
use Carbon\Carbon;
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

class SendDayBeforeReminder extends Command
{
    protected $signature = 'interviews:remind-day-before';
    protected $description = 'Send LINE reminders 1 day before scheduled interviews with confirm button';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $interviews = Interview::with('applicant')
            ->whereDate('interview_date', $tomorrow)
            ->whereIn('status', ['scheduled', 'time_confirmed'])
            ->where('day_before_reminder_sent', false)
            ->get();

        if ($interviews->isEmpty()) {
            $this->info('No interviews tomorrow to remind.');
            return;
        }

        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        foreach ($interviews as $interview) {
            $applicant = $interview->applicant;
            if (!$applicant || !$applicant->line_user_id) {
                continue;
            }

            $dateFormatted = Carbon::parse($interview->interview_date)->format('d/m/Y');

            $flexBubble = new FlexBubble([
                'type' => 'bubble',
                'body' => new FlexBox([
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        new FlexText(['type' => 'text', 'text' => '🔔 แจ้งเตือนล่วงหน้า 1 วัน', 'weight' => 'bold', 'size' => 'lg', 'color' => '#e67e22']),
                        new FlexText(['type' => 'text', 'text' => "สวัสดีครับ คุณ {$applicant->name}", 'margin' => 'md']),
                        new FlexText(['type' => 'text', 'text' => "พรุ่งนี้คุณมีนัดหมายงาน", 'margin' => 'sm', 'color' => '#555555']),
                        new FlexText(['type' => 'text', 'text' => "📅 วันที่: {$dateFormatted}", 'margin' => 'md']),
                        new FlexText(['type' => 'text', 'text' => "⏰ เวลา: {$interview->interview_time}", 'margin' => 'sm']),
                        new FlexText(['type' => 'text', 'text' => "📍 สถานที่: {$interview->location}", 'margin' => 'sm', 'wrap' => true]),
                        new FlexText(['type' => 'text', 'text' => "📌 รบกวนกดยืนยันการเข้าร่วมก่อนเวลานัดหมาย 3 ชม.", 'margin' => 'md', 'size' => 'sm', 'color' => '#e83e8c', 'weight' => 'bold', 'wrap' => true]),
                        new FlexText(['type' => 'text', 'text' => "กรุณายืนยันว่าคุณจะมานะครับ 👇", 'margin' => 'md', 'size' => 'sm', 'color' => '#888888']),
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
                            'color' => '#06c755',
                            'action' => new PostbackAction([
                                'type' => 'postback',
                                'label' => '✅ ยืนยันมาแน่นอน',
                                'data' => "action=day_confirm&interview_id={$interview->id}",
                            ])
                        ]),
                        new FlexButton([
                            'type' => 'button',
                            'style' => 'primary',
                            'color' => '#dc3545',
                            'action' => new PostbackAction([
                                'type' => 'postback',
                                'label' => '❌ ไม่สะดวก',
                                'data' => "action=day_cancel&interview_id={$interview->id}",
                            ])
                        ]),
                    ]
                ])
            ]);

            $message = new FlexMessage([
                'type' => 'flex',
                'altText' => '🔔 แจ้งเตือน: พรุ่งนี้คุณมีนัดหมายงาน',
                'contents' => $flexBubble,
            ]);

            $request = new PushMessageRequest([
                'to' => $applicant->line_user_id,
                'messages' => [$message],
            ]);

            try {
                $messagingApi->pushMessage($request);
                $interview->update(['day_before_reminder_sent' => true]);
                $this->info("Day-before reminder sent to {$applicant->name}");
            } catch (\Exception $e) {
                Log::error("Failed to send day-before reminder to {$applicant->name}: " . $e->getMessage());
                $this->error("Failed to send to {$applicant->name}: " . $e->getMessage());
            }
        }
    }
}
