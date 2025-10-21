<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use App\Models\Pemesanan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:expire-pending', function () {
    $hours = (int) env('BOOKING_PENDING_EXPIRY_HOURS', 6);
    $cutoff = Carbon::now()->subHours(max(1, $hours));

    $expired = Pemesanan::where('status', 'pending')
        ->where('created_at', '<', $cutoff)
        ->update(['status' => 'cancelled', 'updated_at' => now()]);

    $this->info("Expired {$expired} pending bookings older than {$hours}h.");
})->purpose('Expire pending bookings older than configured hours');
