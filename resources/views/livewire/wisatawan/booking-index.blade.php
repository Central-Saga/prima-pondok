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

        // Anggap semua pemesanan pending tanpa pembayaran sebagai hangus
        // ketika pelanggan kembali melihat daftar booking (misalnya lewat tombol back browser)
        Pemesanan::where('wisatawan_id', $wisatawanId)
            ->where('status', Pemesanan::STATUS_PENDING)
            ->whereDoesntHave('pembayaran')
            ->update(['status' => Pemesanan::STATUS_CANCELLED]);

        return Pemesanan::with('kamar')
            ->where('wisatawan_id', $wisatawanId)
            // Sembunyikan pemesanan yang dibatalkan oleh pelanggan dari daftar utama
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
                <select wire:model="status" class="ui-select w-40">
                    <option value="all">{{ __('booking.filter_all') }}</option>
                    <option value="pending">{{ __('booking.filter_pending') }}</option>
                    <option value="confirmed">{{ __('booking.filter_confirmed') }}</option>
                    <option value="cancelled">{{ __('booking.filter_cancelled') }}</option>
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
                                {{-- <a href="{{ route('booking.show', $row->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">Detail</a> --}}
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
