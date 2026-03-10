<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Today: " . now()->format('Y-m-d H:i:s') . "\n\n";

echo "=== Testing isDayPast logic ===\n";

// Test January 2026 (current month with past dates)
$currentMonth = 1;
$currentYear = 2026;

for ($day = 1; $day <= 31; $day++) {
    $cd = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
    $isPast = $cd->isPast() && !$cd->isToday();
    $status = $isPast ? "PAST (should be disabled)" : "FUTURE (available)";
    echo "Jan {$day}, 2026: {$status}\n";
}

echo "\n=== Testing February 2026 (future month) ===\n";
$currentMonth = 2;
for ($day = 1; $day <= 10; $day++) {
    $cd = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
    $isPast = $cd->isPast() && !$cd->isToday();
    $status = $isPast ? "PAST (should be disabled)" : "FUTURE (available)";
    echo "Feb {$day}, 2026: {$status}\n";
}
