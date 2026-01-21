<?php

namespace App\Events;

use App\Models\Pemesanan;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PemesananCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Pemesanan $pemesanan)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.pemesanan'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'PemesananCreated';
    }

    public function broadcastWith(): array
    {
        $p = $this->pemesanan->loadMissing(['kamar:id,nama_kamar', 'wisatawan:id,name']);

        return [
            'id' => $p->id,
            'kamar' => $p->kamar?->nama_kamar,
            'wisatawan' => $p->wisatawan?->name,
            'checkin' => optional($p->tanggal_checkin)->format('Y-m-d'),
            'checkout' => optional($p->tanggal_checkout)->format('Y-m-d'),
            'created_at' => optional($p->created_at)->toISOString(),
        ];
    }
}
