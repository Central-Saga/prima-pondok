<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $status = 'all';
    public string $q = '';

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
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pemesanan</h1>
            <p class="mt-1 ui-help">Daftar pemesanan terbaru. Gunakan filter status untuk menyaring.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari tamu/kamar/#id" class="ui-input w-56" />
            <select wire:model="status" class="ui-select">
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
                        <a href="{{ route('admin.pemesanan.show', $row->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">Detail</a>
                        <a href="{{ route('admin.pemesanan.edit', $row->id) }}" class="ml-2 inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-50">Edit</a>
                        <button onclick="if(!confirm('Hapus pemesanan ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->items->links() }}
    </div>
</section>
