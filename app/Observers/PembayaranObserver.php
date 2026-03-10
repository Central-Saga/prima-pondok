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
            // For extend bookings, set status to confirmed (admin verifies extend payment)
            if ($booking->is_extend && $booking->status !== Pemesanan::STATUS_CONFIRMED) {
                $booking->update(['status' => Pemesanan::STATUS_CONFIRMED]);

                // Also complete the parent booking since extend is now confirmed
                if ($booking->extend_from_id) {
                    $parent = Pemesanan::find($booking->extend_from_id);
                    if ($parent && $parent->status === Pemesanan::STATUS_EXTEND) {
                        $parent->update(['status' => Pemesanan::STATUS_COMPLETED]);
                    }
                }

                return;
            }

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

