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

class SendInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interviews:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send LINE reminders for tomorrow\'s interviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $interviews = Interview::with('applicant')
            ->whereDate('interview_date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        if ($interviews->isEmpty()) {
            $this->info('No interviews confirmed for tomorrow.');
            return;
        }

        $client = new Client();
        $config = new Configuration();
        $config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $messagingApi = new MessagingApiApi(client: $client, config: $config);

        foreach ($interviews as $interview) {
            $applicant = $interview->applicant;
            if (!$applicant || !$applicant->line_user_id)
                continue;

            $text = "แจ้งเตือนการนัดสัมภาษณ์งานพรุ่งนี้ครับ\n\n📅 วันที่: " . Carbon::parse($interview->interview_date)->format('d/m/Y') . "\n⏰ เวลา: {$interview->interview_time}\n📍 สถานที่: {$interview->location}\n\nแล้วพบกันครับ!";

            $message = new TextMessage(['type' => 'text', 'text' => $text]);
            $request = new PushMessageRequest([
                'to' => $applicant->line_user_id,
                'messages' => [$message]
            ]);

            try {
                $messagingApi->pushMessage($request);
                $this->info("Reminder sent to {$applicant->name}");
            } catch (\Exception $e) {
                Log::error("Failed to send reminder to {$applicant->name}: " . $e->getMessage());
            }
        }
    }
}
