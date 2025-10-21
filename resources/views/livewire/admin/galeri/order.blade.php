<?php

use App\Models\Galeri;
use Livewire\Volt\Component;

new class extends Component {
    public $items = [];

    public function mount(): void
    {
        $this->refreshItems();
    }

    private function refreshItems(): void
    {
        $this->items = Galeri::orderBy('urutan')->orderBy('id','desc')
            ->get(['id','title','urutan'])->toArray();
    }

    public function reorder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            Galeri::whereKey($id)->update(['urutan' => $index + 1]);
        }
        $this->refreshItems();
        session()->flash('saved', 'Urutan galeri disimpan.');
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Urutan Galeri</h1>
            <p class="mt-1 ui-help">Ubah nomor urut lalu klik Simpan.</p>
        </div>
        <a href="{{ route('admin.galeri.index') }}" class="ui-btn-secondary">Kembali</a>
    </div>

    @if (session('saved'))
        <div class="mt-4 rounded-lg bg-emerald-50 text-emerald-700 p-3 text-sm">{{ session('saved') }}</div>
    @endif

    <div class="mt-6 ui-card" x-data x-init="
        (() => {
            if (window.Sortable) {
                const el = $refs.sortable;
                new Sortable(el, {
                    animation: 150,
                    handle: '.drag-handle',
                    onEnd: function() {
                        const ids = Array.from(el.querySelectorAll('[data-id]')).map(li => li.getAttribute('data-id'));
                        $wire.reorder(ids);
                    }
                });
            }
        })()
    ">
        <template x-if="!window.Sortable">
            <div class="rounded-md bg-amber-50 text-amber-700 p-3 text-sm">
                Memuat library drag-and-drop…
            </div>
        </template>

        <ul class="divide-y" x-ref="sortable">
            @foreach($items as $row)
                <li class="flex items-center justify-between py-2" data-id="{{ $row['id'] }}">
                    <div class="flex items-center gap-3">
                        <button type="button" class="drag-handle cursor-grab select-none inline-flex items-center rounded-md bg-sky-50 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200">☰</button>
                        <span class="text-slate-900">{{ $row['title'] ?: 'Tanpa judul' }}</span>
                    </div>
                    <span class="text-xs text-slate-500">#{{ $row['urutan'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>
</section>
