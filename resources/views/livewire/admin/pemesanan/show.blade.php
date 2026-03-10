<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;

new class extends Component {
    public Pemesanan $pemesanan;

    public function mount(Pemesanan $pemesanan): void
    {
        $this->pemesanan = $pemesanan->load(['kamar','wisatawan','pembayaran','extensions']);
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
                    <x-status-badge :status="$pemesanan->status" />

                    @if($pemesanan->is_extend)
                        <span class="inline-flex items-center rounded-full bg-violet-50 px-2 py-0.5 text-xs font-medium text-violet-700 ring-1 ring-inset ring-violet-200">EXT #{{ $pemesanan->extend_from_id }}</span>
                    @endif
                </div>
            </div>

            {{-- Checkout Status --}}
            @if(in_array($pemesanan->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                @php
                    $checkoutStatus = $pemesanan->checkout_status;
                @endphp
                @if($checkoutStatus === \App\Models\Pemesanan::CHECKOUT_STATUS_OVERDUE)
                    <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-3">
                        <div class="flex items-center gap-2">
                            <x-status-badge status="jatuh_tempo" />
                            <span class="text-sm font-medium text-rose-700">Lewat batas checkout — Denda Rp {{ number_format(\App\Models\Pemesanan::DENDA_AMOUNT,0,',','.') }}</span>
                        </div>
                        <p class="mt-1 text-xs text-rose-600">Checkout: {{ $pemesanan->getCheckoutDeadline()->format('d M Y H:i') }}</p>
                    </div>
                @elseif($checkoutStatus === \App\Models\Pemesanan::CHECKOUT_STATUS_WARNING)
                    <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-3">
                        <div class="flex items-center gap-2">
                            <x-status-badge status="diperingati" />
                            <span class="text-sm font-medium text-amber-700">Mendekati batas checkout</span>
                        </div>
                        <p class="mt-1 text-xs text-amber-600">Checkout: {{ $pemesanan->getCheckoutDeadline()->format('d M Y H:i') }}</p>
                    </div>
                @endif
            @endif

            {{-- Extend Bookings List --}}
            @if($pemesanan->extensions && $pemesanan->extensions->count())
                <div class="mt-4">
                    <div class="ui-label">Extend Bookings</div>
                    <div class="mt-1 space-y-1">
                        @foreach($pemesanan->extensions as $ext)
                            <a href="{{ route('admin.pemesanan.show', $ext) }}" class="block text-sm text-violet-700 hover:underline">
                                #{{ $ext->id }} — {{ $ext->tanggal_checkin->format('d M') }} s/d {{ $ext->tanggal_checkout->format('d M Y') }}
                                <x-status-badge :status="$ext->status" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="ui-card space-y-3">
            <div class="text-sm font-medium text-slate-900">Aksi</div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="mark('confirmed')" class="ui-btn-primary">Tandai Confirmed</button>
                <button wire:click="mark('cancelled')" class="inline-flex items-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white hover:bg-rose-500">Batalkan</button>
                <button wire:click="mark('completed')" class="ui-btn-secondary">Tandai Completed</button>
                @if(in_array($pemesanan->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                    <button wire:click="mark('completed')" wire:confirm="Checkout tamu ini? Status akan berubah menjadi completed." class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">
                        <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3h-6m0 0l3-3m-3 3l3 3"/></svg>
                        Checkout
                    </button>
                @endif
            </div>

            @php
                $latestPayment = ($pemesanan->pembayaran ?? collect())->sortByDesc('id')->first();
                $proof = $latestPayment?->bukti_pembayaran;
                $proofUrl = null;
                if ($proof) {
                    $proofUrl = \Illuminate\Support\Str::startsWith($proof, ['http://','https://'])
                        ? $proof
                        : url('media/'.$proof);
                }
                $lowerProof = $proof ? strtolower($proof) : '';
                $isImage = $proof && \Illuminate\Support\Str::endsWith($lowerProof, ['.jpg','.jpeg','.png','.gif','.webp']);
            @endphp
            <div class="pt-4 border-t">
                <div class="text-sm font-medium text-slate-900">Bukti Pembayaran</div>
                @if($proofUrl)
                    <div class="mt-2 text-sm flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $latestPayment?->metode_bayar ?? '-' }} - Rp {{ number_format($latestPayment?->jumlah ?? 0,0,',','.') }}</div>
                            <div class="text-slate-600 text-xs capitalize">Status: {{ $latestPayment?->status ?? '-' }}</div>
                        </div>
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="text-sky-700 hover:underline">Lihat File</a>
                    </div>
                    @if($isImage)
                        <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="mt-3 block">
                            <img src="{{ $proofUrl }}" alt="Bukti pembayaran" class="w-full max-h-72 rounded-lg border object-contain bg-white" />
                        </a>
                    @endif
                @else
                    <div class="mt-2 ui-help">Belum ada bukti pembayaran.</div>
                @endif
            </div>
        </div>
    </div>
</section>
