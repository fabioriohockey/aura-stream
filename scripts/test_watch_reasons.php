<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$episode = App\Models\Episode::first();
$user = App\Models\User::first();

echo "Episode: {$episode->id} - {$episode->title}\n";

function showReason($ep, $user) {
    $r = $ep->canUserWatchReason($user);
    echo "User: " . ($user? $user->email : 'null') . " => reason: ";
    echo ($r ?: 'can_watch') . "\n";
}

// Not logged in
showReason($episode, null);

// Normal user
$user->subscription_type = 'free';
$user->episodes_watched_today = 0;
$user->last_watch_date = now()->toDateString();
$user->save();
showReason($episode, $user);

// Reach daily limit for free user
$user->episodes_watched_today = 1;
$user->last_watch_date = now()->toDateString();
$user->save();
showReason($episode, $user);

// Premium only episode test
$ep2 = $episode;
$ep2->is_premium_only = true;
$ep2->save();
showReason($ep2, $user);

// Make user premium
$user->subscription_type = 'premium';
$user->save();
showReason($ep2, $user);

echo "Done\n";