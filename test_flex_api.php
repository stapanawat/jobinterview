<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

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
use App\Models\Applicant;
use App\Models\Interview;

$client = new Client();
$config = new Configuration();
$config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
$messagingApi = new MessagingApiApi(client: $client, config: $config);

// Get the latest applicant
$applicant = Applicant::whereNotNull('line_user_id')->orderBy('id', 'desc')->first();

if (!$applicant) {
    echo "No applicant with line_user_id found!\n";
    exit;
}

echo "Using Applicant ID: {$applicant->id}, LINE ID: {$applicant->line_user_id}\n";

$interview = new Interview([
    'id' => 999,
    'interview_date' => date('Y-m-d'),
    'interview_time' => '10:00:00',
    'location' => 'Office',
]);

$dateFormatted = \Carbon\Carbon::parse($interview->interview_date)->format('d/m/Y');

$flexBubble = new FlexBubble([
    'type' => 'bubble',
    'body' => new FlexBox([
        'type' => 'box',
        'layout' => 'vertical',
        'contents' => [
            new FlexText(['type' => 'text', 'text' => 'นัดหมาย', 'weight' => 'bold', 'size' => 'xl']),
            new FlexText(['type' => 'text', 'text' => "ถึงคุณ ผู้สมัคร", 'margin' => 'md']),
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
            ])
        ]
    ])
]);

$message = new FlexMessage([
    'type' => 'flex',
    'altText' => 'นัดหมาย',
    'contents' => $flexBubble
]);

$request = new PushMessageRequest([
    'to' => $applicant->line_user_id,
    'messages' => [$message]
]);

try {
    $messagingApi->pushMessage($request);
    echo "Push message sent successfully!\n";
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Client Exception! Response Body:\n";
    echo $e->getResponse()->getBody()->getContents() . "\n";
} catch (\Exception $e) {
    echo "Exception:\n";
    echo $e->getMessage() . "\n";
}
