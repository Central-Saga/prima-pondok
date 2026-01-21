<?php

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new #[Layout('components.layouts.public')] class extends Component {
    use WithPagination;

    public string $status = 'all';

    public function updatedStatus(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        $user = Auth::user();
        $wisatawanId = $user?->wisatawan?->id;

        // Jika sudah ada pembayaran (pending/rejected), pastikan status booking tidak tetap "cancelled"
        Pemesanan::where('wisatawan_id', $wisatawanId)
            ->where('status', Pemesanan::STATUS_CANCELLED)
            ->whereHas('pembayaran', function ($q) {
                $q->whereIn('status', ['pending', 'rejected']);
            })
            ->update(['status' => Pemesanan::STATUS_PENDING]);

        return Pemesanan::with(['kamar','review'])
            ->where('wisatawan_id', $wisatawanId)
            ->where('status', '!=', Pemesanan::STATUS_CANCELLED)
            ->when($this->status !== 'all', fn($q) => $q->where('status',$this->status))
            ->latest()
            ->paginate(10);
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('booking.history_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('booking.history_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <select wire:model.live.debounce.1000ms="status" class="ui-select w-40">
                    <option value="all">{{ __('booking.filter_all') }}</option>
                    <option value="pending">{{ __('booking.filter_pending') }}</option>
                    <option value="confirmed">{{ __('booking.filter_confirmed') }}</option>
                    <option value="completed">{{ __('booking.filter_completed') }}</option>
                </select>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="py-2 px-3 text-left">{{ __('booking.code') }}</th>
                        <th class="py-2 px-3 text-left">{{ __('booking.room') }}</th>
                        <th class="py-2 px-3 text-left">{{ __('booking.checkin') }}</th>
                        <th class="py-2 px-3 text-left">{{ __('booking.checkout') }}</th>
                        <th class="py-2 px-3 text-left">{{ __('booking.total_header') }}</th>
                        <th class="py-2 px-3 text-left">{{ __('booking.status_header') }}</th>
                        <th class="py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($this->items as $row)
                        <tr>
                            <td class="py-2 px-3">#{{ $row->id }}</td>
                            <td class="py-2 px-3">{{ $row->kamar->nama_kamar ?? '-' }}</td>
                            <td class="py-2 px-3">{{ optional($row->tanggal_checkin)->format('d M Y') }}</td>
                            <td class="py-2 px-3">{{ optional($row->tanggal_checkout)->format('d M Y') }}</td>
                            <td class="py-2 px-3">Rp {{ number_format($row->total_bayar,0,',','.') }}</td>
                            <td class="py-2 px-3"><x-status-badge :status="$row->status" /></td>
                            <td class="py-2 px-3 text-right">
                                @php
                                    $eligibleStatus = in_array($row->status, ['confirmed','completed'], true);
                                    $canReview = $eligibleStatus;
                                @endphp

                                @if(! $eligibleStatus)
                                    <a href="{{ route('booking.show', $row->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                        {{ __('booking.view_detail') }}
                                    </a>
                                @endif

                                @if($eligibleStatus)
                                    <a href="{{ route('booking.print', $row->id) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-50">
                                        {{ __('booking.print_proof') }}
                                    </a>
                                @endif

                                @if($canReview)
                                    @if($row->review)
                                        <span class="{{ (! $eligibleStatus && ! empty($row->review)) ? 'ml-2' : '' }} inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 bg-emerald-50">
                                            {{ __('reviews.thanks') }}
                                        </span>
                                    @else
                                        <a href="{{ route('booking.review', $row->id) }}" class="{{ ! $eligibleStatus ? 'ml-2' : '' }} inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">
                                            {{ __('reviews.write') }}
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-6 px-3 text-center text-slate-600" colspan="7">{{ __('booking.no_bookings') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->items->links() }}
        </div>
    </div>
</section>
