<?php

use App\Models\Pembayaran;
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
        return Pembayaran::with('pemesanan.kamar')
            ->when($this->status !== 'all', fn($q) => $q->where('status', $this->status))
            ->when($this->q !== '', function ($q) {
                $term = '%'.$this->q.'%';
                $q->whereHas('pemesanan.kamar', fn($k) => $k->where('nama_kamar','like',$term))
                  ->orWhere('pemesanan_id', $this->q);
            })
            ->orderByRaw("FIELD(status, 'pending','rejected','verified')")
            ->latest()
            ->paginate(10);
    }

    public function verify(int $id): void
    {
        $pay = Pembayaran::findOrFail($id);
        $pay->update(['status' => Pembayaran::STATUS_VERIFIED]);
        Pemesanan::whereKey($pay->pemesanan_id)->update(['status' => Pemesanan::STATUS_CONFIRMED]);
        $this->resetPage();
    }

    public function reject(int $id): void
    {
        Pembayaran::whereKey($id)->update(['status' => Pembayaran::STATUS_REJECTED]);
        $this->resetPage();
    }
    public function delete(int $id): void
    {
        Pembayaran::whereKey($id)->delete();
        $this->resetPage();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Verifikasi Pembayaran</h1>
            <p class="mt-1 text-slate-600 text-sm">Tinjau bukti pembayaran dan ubah status.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari kamar/#id..." class="ui-input w-56" />
            <select wire:model="status" class="ui-select w-40">
                <option value="all">Semua</option>
                <option value="pending">Pending</option>
                <option value="verified">Verified</option>
                <option value="rejected">Rejected</option>
            </select>
            <a href="{{ route('admin.pembayaran.create') }}" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Pemesanan</th>
                    <th class="py-2 px-3 text-left">Jumlah</th>
                    <th class="py-2 px-3 text-left">Metode</th>
                    <th class="py-2 px-3 text-left">Bukti</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $pay)
                    <tr>
                        <td class="py-2 px-3">
                            {{ $pay->pemesanan->kamar->nama_kamar }} — #{{ $pay->pemesanan_id }}
                        </td>
                        <td class="py-2 px-3">Rp {{ number_format($pay->jumlah,0,',','.') }}</td>
                        <td class="py-2 px-3">{{ $pay->metode_bayar }}</td>
                        <td class="py-2 px-3">
                            @if(\Illuminate\Support\Str::startsWith($pay->bukti_pembayaran, ['http://','https://']))
                                <a class="text-sky-700 underline" href="{{ $pay->bukti_pembayaran }}" target="_blank">Lihat</a>
                            @else
                                <a class="text-sky-700 underline" href="{{ url('media/'.$pay->bukti_pembayaran) }}" target="_blank">Lihat</a>
                            @endif
                        </td>
                        <td class="py-2 px-3"><x-status-badge :status="$pay->status" /></td>
                        <td class="py-2 px-3 text-right">
                            @if($pay->status === 'pending')
                                <button wire:click="verify({{ $pay->id }})" class="inline-flex items-center rounded-md bg-sky-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-sky-500 mr-2">Verify</button>
                                <button wire:click="reject({{ $pay->id }})" class="inline-flex items-center rounded-md bg-rose-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-rose-500">Reject</button>
                            @else
                                <a href="{{ route('admin.pembayaran.edit', $pay->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-50 mr-2">Edit</a>
                                <button onclick="if(!confirm('Hapus pembayaran ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $pay->id }})" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                            @endif
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
