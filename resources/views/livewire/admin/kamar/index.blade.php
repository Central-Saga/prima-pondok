<?php

use App\Models\Kamar;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $q = '';

    public function updatingQ(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Kamar::query()
            ->when($this->q !== '', fn($q) => $q->where(function($x){
                $x->where('nama_kamar','like','%'.$this->q.'%')
                  ->orWhere('tipe_kamar','like','%'.$this->q.'%');
            }))
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function delete(int $id): void
    {
        Kamar::whereKey($id)->delete();
        $this->resetPage();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Kamar</h1>
            <p class="mt-1 text-slate-600 text-sm">Kelola data kamar dan status ketersediaan.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari kamar..." class="ui-input w-56" />
            <a href="{{ route('admin.kamar.create') }}" class="ui-btn-primary">Tambah Kamar</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Nama</th>
                    <th class="py-2 px-3 text-left">Tipe</th>
                    <th class="py-2 px-3 text-left">Harga</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $row)
                    <tr>
                        <td class="py-2 px-3">{{ $row->nama_kamar }}</td>
                        <td class="py-2 px-3">{{ $row->tipe_kamar }}</td>
                        <td class="py-2 px-3">Rp {{ number_format($row->harga,0,',','.') }}</td>
                        <td class="py-2 px-3"><x-status-badge :status="$row->status" /></td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.kamar.edit', $row->id) }}" class="ui-btn-secondary">Edit</a>
                            <button onclick="if(!confirm('Hapus kamar ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
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
