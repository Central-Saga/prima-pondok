<?php

use App\Models\Bank;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $q = '';

    public function updatingQ(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Bank::query()
            ->when($this->q !== '', fn($q) => $q->where('nama_bank','like','%'.$this->q.'%')->orWhere('no_transfer','like','%'.$this->q.'%'))
            ->orderBy('nama_bank')
            ->paginate(10);
    }

    public function delete(int $id): void
    {
        Bank::whereKey($id)->delete();
        $this->resetPage();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Bank</h1>
            <p class="mt-1 text-slate-600 text-sm">Kelola nama bank dan nomor transfer.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.800ms="q" placeholder="Cari bank/no..." class="ui-input w-56" />
            <a href="{{ route('admin.bank.create') }}" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Nama Bank</th>
                    <th class="py-2 px-3 text-left">No Transfer</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $row)
                    <tr>
                        <td class="py-2 px-3">{{ $row->nama_bank }}</td>
                        <td class="py-2 px-3 font-mono">{{ $row->no_transfer }}</td>
                        <td class="py-2 px-3"><x-status-badge :status="$row->status" /></td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.bank.edit', $row->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200 hover:bg-amber-50">Edit</a>
                            <button onclick="if(!confirm('Hapus bank ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
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

