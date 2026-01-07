<?php

use App\Models\Review;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $q = '';
    public string $status = 'all'; // all|published|hidden

    public function updatingQ(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Review::query()
            ->with(['pemesanan','kamar','wisatawan'])
            ->when($this->status === 'published', fn ($q) => $q->where('is_published', true))
            ->when($this->status === 'hidden', fn ($q) => $q->where('is_published', false))
            ->when($this->q !== '', function ($q) {
                $term = '%'.$this->q.'%';
                $q->where('komentar', 'like', $term)
                    ->orWhereHas('kamar', fn ($k) => $k->where('nama_kamar', 'like', $term))
                    ->orWhereHas('wisatawan', fn ($w) => $w->where('name', 'like', $term))
                    ->orWhere('pemesanan_id', $this->q);
            })
            ->latest()
            ->paginate(10);
    }

    public function togglePublish(int $id): void
    {
        $r = Review::findOrFail($id);
        $r->update(['is_published' => ! (bool) $r->is_published]);
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        Review::whereKey($id)->delete();
        $this->resetPage();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Review</h1>
            <p class="mt-1 text-slate-600 text-sm">Kelola review pelanggan dan tentukan yang tampil di landing page.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.800ms="q" placeholder="Cari kamar/nama/komentar/#booking..." class="ui-input w-64" />
            <select wire:model="status" class="ui-select w-40">
                <option value="all">Semua</option>
                <option value="published">Tampil</option>
                <option value="hidden">Tersembunyi</option>
            </select>
            <a href="{{ route('admin.review.create') }}" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Booking</th>
                    <th class="py-2 px-3 text-left">Kamar</th>
                    <th class="py-2 px-3 text-left">Pelanggan</th>
                    <th class="py-2 px-3 text-left">Rating</th>
                    <th class="py-2 px-3 text-left">Tampil</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $row)
                    <tr>
                        <td class="py-2 px-3">#{{ $row->pemesanan_id }}</td>
                        <td class="py-2 px-3">{{ $row->kamar->nama_kamar ?? '-' }}</td>
                        <td class="py-2 px-3">{{ $row->wisatawan->name ?? '-' }}</td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-1">
                                @for($i=1; $i<=5; $i++)
                                    <svg class="h-4 w-4 {{ $row->rating >= $i ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.71c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                                    </svg>
                                @endfor
                            </div>
                        </td>
                        <td class="py-2 px-3">
                            <button wire:click="togglePublish({{ $row->id }})" class="{{ $row->is_published ? 'inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100' : 'inline-flex items-center rounded-lg bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100' }}">
                                {{ $row->is_published ? 'Tampil' : 'Sembunyi' }}
                            </button>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.review.edit', $row->id) }}" class="ui-btn-secondary">Edit</a>
                            <button onclick="if(!confirm('Hapus review ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
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

