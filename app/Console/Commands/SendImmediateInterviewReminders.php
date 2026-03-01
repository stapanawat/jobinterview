<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Interview;
use Carbon\Carbon;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SendImmediateInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interviews:remind-immediate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send LINE reminders 1 hour before scheduled interviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find interviews starting in approximately 1 hour (50-70 minutes from now)
        $now = Carbon::now();
        $startTime = $now->copy()->addMinutes(50)->toTimeString();
        $endTime = $now->copy()->addMinutes(70)->toTimeString();
        $today = $now->toDateString();

        $interviews = Interview::with('applicant')
            ->whereDate('interview_date', $today)
            ->whereBetween('interview_time', [$startTime, $endTime])
            ->where('status', 'confirmed')
            ->where('reminder_sent', false)
            ->get();

        if ($interviews->isEmpty()) {
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

            $flexBubble = new \LINE\Clients\MessagingApi\Model\FlexBubble([
                'type' => 'bubble',
                'body' => new \LINE\Clients\MessagingApi\Model\FlexBox([
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => [
                        new \LINE\Clients\MessagingApi\Model\FlexText(['type' => 'text', 'text' => '🔔 เตือน: อีก 1 ชั่วโมง', 'weight' => 'bold', 'size' => 'lg', 'color' => '#e67e22']),
                        new \LINE\Clients\MessagingApi\Model\FlexText(['type' => 'text', 'text' => "ถึงคุณ {$applicant->name}", 'margin' => 'md']),
                        new \LINE\Clients\MessagingApi\Model\FlexText(['type' => 'text', 'text' => "⏰ เวลา: {$interview->interview_time}", 'margin' => 'sm']),
                        new \LINE\Clients\MessagingApi\Model\FlexText(['type' => 'text', 'text' => "📍 สถานที่: {$interview->location}", 'margin' => 'sm', 'wrap' => true]),
                        new \LINE\Clients\MessagingApi\Model\FlexText(['type' => 'text', 'text' => "กรุณาเตรียมตัวให้พร้อมนะครับ!", 'margin' => 'lg', 'size' => 'sm', 'color' => '#888888']),
                    ],
                ]),
            ]);

            $message = new \LINE\Clients\MessagingApi\Model\FlexMessage([
                'type' => 'flex',
                'altText' => '🔔 แจ้งเตือน: อีก 1 ชั่วโมงจะถึงเวลานัดสัมภาษณ์ครับ',
                'contents' => $flexBubble,
            ]);

            $request = new PushMessageRequest([
                'to' => $applicant->line_user_id,
                'messages' => [$message],
            ]);

            try {
                $messagingApi->pushMessage($request);
                $interview->update(['reminder_sent' => true]);
                $this->info("Immediate reminder sent to {$applicant->name}");
            } catch (\Exception $e) {
                Log::error("Failed to send immediate reminder to {$applicant->name}: " . $e->getMessage());
            }
        }
    }
}
