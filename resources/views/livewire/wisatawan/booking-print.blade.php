<?php

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public Pemesanan $pemesanan;

    public function mount(Pemesanan $pemesanan): void
    {
        $user = Auth::user();
        $wisatawanId = $user?->wisatawan?->id;
        abort_if(! $wisatawanId || $pemesanan->wisatawan_id !== $wisatawanId, 403);

        abort_if(! in_array($pemesanan->status, [Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_COMPLETED], true), 404);

        $this->pemesanan = $pemesanan->load(['kamar', 'wisatawan']);
    }
}; ?>

<div>
    <section class="py-10 bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="print:hidden flex items-center justify-between gap-3">
                <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('booking.back') }}</a>
                <button type="button" onclick="window.print()" class="ui-btn-primary">{{ __('booking.print_proof') }}</button>
            </div>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ __('booking.print_title') }}</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-900">{{ $pemesanan->kamar->nama_kamar ?? '-' }}</div>
                        <div class="mt-1 text-sm text-slate-600">{{ __('booking.print_subtitle') }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-600">{{ __('booking.code') }}</div>
                        <div class="text-xl font-semibold text-slate-900">#{{ $pemesanan->id }}</div>
                        <div class="mt-2"><x-status-badge :status="$pemesanan->status" /></div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-slate-500">{{ __('booking.guest_name') }}</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ $pemesanan->wisatawan->name ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-slate-500">{{ __('booking.duration') }}</div>
                        <div class="mt-1 font-semibold text-slate-900">
                            {{ $pemesanan->jumlah_hari }} {{ trans_choice('booking.nights', $pemesanan->jumlah_hari) }}
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-slate-500">{{ __('booking.checkin') }}</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ optional($pemesanan->tanggal_checkin)->format('d M Y') }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="text-slate-500">{{ __('booking.checkout') }}</div>
                        <div class="mt-1 font-semibold text-slate-900">{{ optional($pemesanan->tanggal_checkout)->format('d M Y') }}</div>
                    </div>
                </div>

                <div class="mt-6 rounded-xl border border-slate-200 p-4 text-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-slate-600">{{ __('booking.total') }}</div>
                        <div class="text-lg font-semibold text-slate-900">Rp {{ number_format($pemesanan->total_bayar,0,',','.') }}</div>
                    </div>
                    <div class="mt-2 text-xs text-slate-500">{{ __('booking.print_note') }}</div>
                </div>

                <div class="mt-6 text-xs text-slate-500">
                    <div>{{ __('booking.print_footer') }}</div>
                    <div class="mt-1">{{ config('app.name') }} · {{ now()->format('d M Y H:i') }}</div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @media print {
            @page { margin: 12mm; }
            body { background: #fff !important; }
            header[data-site-header] { display: none !important; }
            footer { display: none !important; }
            .print\:hidden { display: none !important; }
            a[href]:after { content: "" !important; }
        }
    </style>
</div>
