<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $status = 'all';
    public string $q = '';
    public ?int $checkoutId = null;

    public function updatingQ(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Pemesanan::with(['kamar','wisatawan'])
            ->when($this->status !== 'all', fn($q) => $q->where('status',$this->status))
            ->when($this->q !== '', function($q){
                $term = '%'.$this->q.'%';
                $q->whereHas('wisatawan', fn($w) => $w->where('name','like',$term))
                  ->orWhereHas('kamar', fn($k) => $k->where('nama_kamar','like',$term))
                  ->orWhere('id', $this->q);
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(int $id): void
    {
        Pemesanan::whereKey($id)->delete();
        $this->resetPage();
    }

    public function checkout(int $id): void
    {
        $booking = Pemesanan::find($id);
        if (! $booking) {
            return;
        }

        if ($booking->status !== Pemesanan::STATUS_CONFIRMED) {
            return;
        }

        $booking->update(['status' => Pemesanan::STATUS_COMPLETED]);
        $this->resetPage();
    }

    public function confirmCheckout(int $id): void
    {
        $this->checkoutId = $id;
    }

    public function cancelCheckout(): void
    {
        $this->checkoutId = null;
    }

    public function doCheckout(): void
    {
        if (! $this->checkoutId) {
            return;
        }

        $this->checkout($this->checkoutId);
        $this->checkoutId = null;
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pemesanan</h1>
            <p class="mt-1 ui-help">Daftar pemesanan terbaru. Gunakan filter status untuk menyaring.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari tamu/kamar/#id" class="ui-input w-56" />
            <select wire:model.live.debounce.1000ms="status" class="ui-select">
                <option value="all">Semua</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
            </select>
            <a href="{{ route('admin.pemesanan.create') }}" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Kode</th>
                    <th class="py-2 px-3 text-left">Kamar</th>
                    <th class="py-2 px-3 text-left">Wisatawan</th>
                    <th class="py-2 px-3 text-left">Check-in</th>
                    <th class="py-2 px-3 text-left">Check-out</th>
                    <th class="py-2 px-3 text-left">Total</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $row)
                <tr>
                    <td class="py-2 px-3">#{{ $row->id }}</td>
                    <td class="py-2 px-3">{{ $row->kamar->nama_kamar }}</td>
                    <td class="py-2 px-3">{{ $row->wisatawan->name ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $row->tanggal_checkin->format('d M Y') }}</td>
                    <td class="py-2 px-3">{{ $row->tanggal_checkout->format('d M Y') }}</td>
                    <td class="py-2 px-3">Rp {{ number_format($row->total_bayar,0,',','.') }}</td>
                    <td class="py-2 px-3"><x-status-badge :status="$row->status" /></td>
                    <td class="py-2 px-3 text-right">
                        <a href="{{ route('admin.pemesanan.show', $row->id) }}" class="ui-btn-secondary">Detail</a>
                        <a href="{{ route('admin.pemesanan.edit', $row->id) }}" class="ml-2 ui-btn-secondary">Edit</a>
                        @if($row->status === \App\Models\Pemesanan::STATUS_CONFIRMED)
                            <button wire:click="confirmCheckout({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Check-out</button>
                        @endif
                        <button onclick="if(!confirm('Hapus pemesanan ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->items->links() }}
    </div>

    @if($checkoutId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" wire:click="cancelCheckout" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
            <div role="dialog" aria-modal="true" class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Konfirmasi Check-out</div>
                <p class="mt-1 text-sm text-slate-600">Tandai booking ini sebagai <span class="font-medium">completed</span> (check-out)?</p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" wire:click="cancelCheckout" class="ui-btn-secondary">Batal</button>
                    <button type="button" wire:click="doCheckout" class="ui-btn-primary !bg-emerald-600 hover:!bg-emerald-500">Ya, Check-out</button>
                </div>
            </div>
        </div>
    @endif
</section>
