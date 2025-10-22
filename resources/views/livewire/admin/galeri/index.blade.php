<?php

use App\Models\Galeri;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $q = '';
    public string $status = 'all';

    public function updatingQ(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Galeri::query()
            ->when($this->q !== '', fn($q) => $q->where('title','like','%'.$this->q.'%'))
            ->when($this->status !== 'all', fn($q) => $q->where('status',$this->status))
            ->orderBy('urutan')
            ->orderByDesc('id')
            ->paginate(12);
    }

    public function delete(int $id): void
    {
        Galeri::whereKey($id)->delete();
        $this->resetPage();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Galeri</h1>
            <p class="mt-1 text-slate-600 text-sm">Atur foto yang tampil di landing page.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari..." class="ui-input w-52" />
            <select wire:model="status" class="ui-select w-40">
                <option value="all">Semua</option>
                <option value="active">Active</option>
                <option value="hidden">Hidden</option>
            </select>
            <a href="{{ route('admin.galeri.create') }}" class="ui-btn-primary">Tambah Gambar</a>
            <a href="{{ route('admin.galeri.order') }}" class="ui-btn-secondary">Ubah Urutan</a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($this->items as $g)
            <div class="rounded-2xl overflow-hidden border border-sky-100 bg-white">
                <div class="aspect-[4/3] bg-slate-100">
                    @if(\Illuminate\Support\Str::startsWith($g->path, ['http://','https://']))
                        <img src="{{ $g->path }}" class="h-full w-full object-cover" />
                    @else
                        <img src="{{ asset('storage/'.ltrim($g->path,'/')) }}" class="h-full w-full object-cover" />
                    @endif
                </div>
                <div class="p-4 text-sm">
                    <div class="font-medium">{{ $g->title }}</div>
                    <div class="text-slate-600">#{{ $g->urutan }} — {{ $g->status }}</div>
                    <div class="mt-3">
                        <a href="{{ route('admin.galeri.edit', $g->id) }}" class="ui-btn-secondary">Edit</a>
                        <button onclick="if(!confirm('Hapus gambar ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $g->id }})" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $this->items->links() }}
    </div>
</section>
