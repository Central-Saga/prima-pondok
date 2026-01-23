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
        ->whereDoesntHave('pembayaran')
        ->where('created_at', '<', $cutoff)
        ->update(['status' => 'cancelled', 'updated_at' => now()]);

    // Safety net: in case there are older records already marked cancelled but have payment activity,
    // keep them in pending so they can be verified / re-uploaded.
    $restored = Pemesanan::where('status', 'cancelled')
        ->whereHas('pembayaran', function ($q) {
            $q->whereIn('status', ['pending', 'rejected']);
        })
        ->update(['status' => 'pending', 'updated_at' => now()]);

    $this->info("Expired {$expired} pending bookings older than {$hours}h. Restored {$restored} cancelled bookings with payments.");
})->purpose('Expire pending bookings older than configured hours');
