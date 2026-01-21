<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;

new class extends Component {
    public Pemesanan $pemesanan;

    public function mount(Pemesanan $pemesanan): void
    {
        $this->pemesanan = $pemesanan->load(['kamar','wisatawan','pembayaran']);
    }

    public function mark(string $status): void
    {
        $allowed = [
            Pemesanan::STATUS_PENDING,
            Pemesanan::STATUS_CONFIRMED,
            Pemesanan::STATUS_CANCELLED,
            Pemesanan::STATUS_COMPLETED,
        ];
        if (! in_array($status, $allowed, true)) return;
        $this->pemesanan->update(['status' => $status]);
        $this->pemesanan->refresh();
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Detail Pemesanan #{{ $pemesanan->id }}</h1>
            <p class="mt-1 ui-help">Kelola status dan lihat informasi lengkap pemesanan.</p>
        </div>
        <a href="{{ route('admin.pemesanan.index') }}" class="ui-btn-secondary">Kembali</a>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="ui-card lg:col-span-2 text-sm">
            <div class="grid sm:grid-cols-2 gap-3">
                <div>
                    <div class="ui-label">Kamar</div>
                    <div class="mt-1">{{ $pemesanan->kamar->nama_kamar }}</div>
                </div>
                <div>
                    <div class="ui-label">Wisatawan</div>
                    <div class="mt-1">{{ $pemesanan->wisatawan->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="ui-label">Check-in</div>
                    <div class="mt-1">{{ $pemesanan->tanggal_checkin->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="ui-label">Check-out</div>
                    <div class="mt-1">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="ui-label">Jumlah Malam</div>
                    <div class="mt-1">{{ $pemesanan->jumlah_hari }}</div>
                </div>
                <div>
                    <div class="ui-label">Total Bayar</div>
                    <div class="mt-1 font-medium">Rp {{ number_format($pemesanan->total_bayar,0,',','.') }}</div>
                </div>
            </div>

            <div class="mt-6">
                <div class="ui-label">Status</div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 capitalize">{{ $pemesanan->status }}</span>
                </div>
            </div>
        </div>

        <div class="ui-card space-y-3">
            <div class="text-sm font-medium text-slate-900">Aksi</div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="mark('confirmed')" class="ui-btn-primary">Tandai Confirmed</button>
                <button wire:click="mark('cancelled')" class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-500">Batalkan</button>
                <button wire:click="mark('completed')" class="ui-btn-secondary">Tandai Completed</button>
            </div>

            <div class="pt-4 border-t">
                <div class="text-sm font-medium text-slate-900">Pembayaran</div>
                @forelse($pemesanan->pembayaran as $pay)
                    <div class="mt-2 text-sm flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $pay->metode_bayar }} - Rp {{ number_format($pay->jumlah,0,',','.') }}</div>
                            <div class="text-slate-600 text-xs capitalize">Status: {{ $pay->status }}</div>
                        </div>
                    </div>
                @empty
                    <div class="mt-2 ui-help">Belum ada data pembayaran.</div>
                @endforelse

                <div class="mt-3 text-xs text-slate-500">
                    Bukti pembayaran hanya dapat dibuka melalui menu <a href="{{ route('admin.pembayaran') }}" class="text-sky-700 hover:underline">Pembayaran</a>.
                </div>
            </div>
        </div>
    </div>
</section>
