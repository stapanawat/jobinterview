<?php
require __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$userId = $argv[1];
$token = $_ENV['LINE_BOT_CHANNEL_ACCESS_TOKEN'];

$ch = curl_init("https://api.line.me/v2/bot/profile/" . $userId);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $httpCode\n";
echo $response . "\n";
