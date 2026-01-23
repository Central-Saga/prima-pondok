<?php

namespace App\Observers;

use App\Events\PemesananCreated;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Log;

class PemesananObserver
{
    public function created(Pemesanan $pemesanan): void
    {
        if ($pemesanan->status !== Pemesanan::STATUS_PENDING) {
            return;
        }

        try {
            broadcast(new PemesananCreated($pemesanan))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast PemesananCreated failed', [
                'pemesanan_id' => $pemesanan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
