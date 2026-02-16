<?php
header('Content-Type: application/json');

// Channels
$channels = [
    'eyaduddin' => 'UCHrjyKuoG7nQSRE-amT8I8w',
    'canadiangamer' => 'UC4cXh5_kRY7xcZIgglTz9sg'
];

$channel = $_GET['channel'] ?? 'canadiangamer';
$channelId = $channels[$channel] ?? $channels['canadiangamer'];

// Load API key manually from .env
$envFile = __DIR__ . '/.env';
$apiKey = '';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with($line, 'YOUTUBE_API=')) {
            $apiKey = trim(substr($line, strlen('YOUTUBE_API=')));
            break;
        }
    }
}

// If no API key, return empty
if (!$apiKey) {
    echo json_encode([]);
    exit;
}

// Fetch videos
$url = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=6&type=video";

$response = file_get_contents($url);
if (!$response) {
    echo json_encode([]);
    exit;
}

$data = json_decode($response, true);
$videos = [];

if (!empty($data['items'])) {
    foreach ($data['items'] as $item) {
        $videos[] = [
            'title' => $item['snippet']['title'],
            'thumbnail' => $item['snippet']['thumbnails']['medium']['url'] ?? '',
            'videoId' => $item['id']['videoId']
        ];
    }
}

echo json_encode($videos);
