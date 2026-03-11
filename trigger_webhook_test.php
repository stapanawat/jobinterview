<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$channelSecret = env('LINE_BOT_CHANNEL_SECRET');
$body = '{"events": [{"type": "postback", "replyToken": "testToken", "source": {"userId": "U4af4980629..."}, "postback": {"data": "action=confirm&interview_id=1"}}]}';
$hash = hash_hmac('sha256', $body, $channelSecret, true);
$signature = base64_encode($hash);

$ch = curl_init('http://127.0.0.1:8000/webhook/line');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'x-line-signature: ' . $signature
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo "Webhook Response: " . $response . "\n";
