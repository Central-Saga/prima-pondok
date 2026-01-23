<?php

namespace App\Observers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;

class PembayaranObserver
{
    public function created(Pembayaran $pembayaran): void
    {
        $this->syncBookingStatus($pembayaran);
    }

    public function updated(Pembayaran $pembayaran): void
    {
        if (! $pembayaran->wasChanged('status')) {
            return;
        }

        $this->syncBookingStatus($pembayaran);
    }

    private function syncBookingStatus(Pembayaran $pembayaran): void
    {
        $booking = $pembayaran->pemesanan()->first();
        if (! $booking) {
            return;
        }

        if ($pembayaran->status === Pembayaran::STATUS_VERIFIED) {
            if ($booking->status !== Pemesanan::STATUS_CONFIRMED) {
                $booking->update(['status' => Pemesanan::STATUS_CONFIRMED]);
            }

            return;
        }

        if (in_array($pembayaran->status, [Pembayaran::STATUS_PENDING, Pembayaran::STATUS_REJECTED], true)) {
            if ($booking->status === Pemesanan::STATUS_CANCELLED) {
                $booking->update(['status' => Pemesanan::STATUS_PENDING]);
            }
        }
    }
}

