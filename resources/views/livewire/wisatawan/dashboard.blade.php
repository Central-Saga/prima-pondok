<?php

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $pemesanan;

    public function mount(): void
    {
        $user = Auth::user();
        $wisatawan = $user?->wisatawan;
        $this->pemesanan = $wisatawan
            ? Pemesanan::with('kamar')->where('wisatawan_id', $wisatawan->id)->latest()->take(10)->get()
            : collect();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-semibold text-slate-900">Dashboard Wisatawan</h1>

    <div class="mt-6">
        <a href="{{ route('home') }}#kamar" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Cari Kamar</a>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-medium">Pemesanan Terakhir</h2>
        <div class="mt-4 divide-y rounded-xl border bg-white">
            @forelse($pemesanan as $p)
                <div class="p-4 flex items-center justify-between">
                    <div class="text-sm">
                        <div class="font-medium text-slate-900">{{ $p->kamar->nama_kamar }}</div>
                        <div class="text-slate-600">{{ $p->tanggal_checkin->format('d M Y') }} → {{ $p->tanggal_checkout->format('d M Y') }} ({{ $p->jumlah_hari }} malam)</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold">Rp {{ number_format($p->total_bayar,0,',','.') }}</div>
                        <div class="mt-1"><x-status-badge :status="$p->status" /></div>
                        <a href="{{ route('booking.show', $p->id) }}" class="block mt-2 text-xs text-sky-700 hover:underline">Lihat / Bayar</a>
                    </div>
                </div>
            @empty
                <div class="p-4 text-slate-600 text-sm">Belum ada pemesanan.</div>
            @endforelse
        </div>
    </div>
</section>
