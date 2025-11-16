<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$episode = App\Models\Episode::first();
$user = App\Models\User::first();

if (!$episode) {
    echo "No episodes found\n"; exit;
}

echo "Episode ID: {$episode->id}\n";
echo "Title: {$episode->title}\n";
echo "is_active: " . ($episode->is_active ? 'true' : 'false') . "\n";
echo "is_premium_only: " . ($episode->is_premium_only ? 'true' : 'false') . "\n";
echo "video_path_480p: " . ($episode->video_path_480p ?? 'null') . "\n";
echo "video_path_720p: " . ($episode->video_path_720p ?? 'null') . "\n";

if ($user) {
    echo "User email: {$user->email}\n";
    echo "User isPremium: " . ($user->isPremium() ? 'true' : 'false') . "\n";
}
