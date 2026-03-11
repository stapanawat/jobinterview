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

$client = new Client();
$config = new Configuration();
$config->setAccessToken(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
$messagingApi = new MessagingApiApi(client: $client, config: $config);

// Try valid dummy user id, or we will just see if the builder fails
// Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx is a dummy format, line might say invalid user id or invalid payload. Invalid payload comes first usually.
$to = 'U578e9bfbebbd664ed130b92db0000000'; // Fake ID

$flexBubble = new FlexBubble([
            'type' => 'bubble',
            'body' => new FlexBox([
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    new FlexText(['type' => 'text', 'text' => 'นัดหมาย', 'weight' => 'bold', 'size' => 'xl']),
                    new FlexText(['type' => 'text', 'text' => "ถึงคุณ สมชาย ใจดี", 'margin' => 'md']),
                    new FlexText(['type' => 'text', 'text' => " วันที่: Test", 'margin' => 'sm']),
                    new FlexText(['type' => 'text', 'text' => "⏰ เวลา: Test", 'margin' => 'sm']),
                    new FlexText(['type' => 'text', 'text' => " สถานที่: Test", 'margin' => 'sm', 'wrap' => true]),
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
                            'data' => "action=confirm&interview_id=1",
                        ])
                    ]),
                    new FlexButton([
                        'type' => 'button',
                        'style' => 'primary',
                        'color' => '#dc3545',
                        'action' => new PostbackAction([
                            'type' => 'postback',
                            'label' => 'ขอเลื่อน',
                            'data' => "action=reschedule&interview_id=1",
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
            'to' => $to,
            'messages' => [$message]
        ]);

try {
    $messagingApi->pushMessage($request);
    echo "Success!";
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Error:\n";
    echo $e->getResponse()->getBody()->getContents();
} catch (\Exception $e) {
    echo $e->getMessage();
}
