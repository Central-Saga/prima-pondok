<?php

use App\Models\Kamar;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    use WithPagination;

    public string $q = '';

    public function updatingQ(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Kamar::query()
            ->with('fotos')
            ->whereIn('status', ['available', 'maintenance', 'unavailable'])
            ->when($this->q !== '', function ($q) {
                $term = '%'.$this->q.'%';
                $q->where(function ($x) use ($term) {
                    $x->where('nama_kamar', 'like', $term)
                      ->orWhere('tipe_kamar', 'like', $term)
                      ->orWhere('deskripsi', 'like', $term)
                      ->orWhere('deskripsi_en', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate(9);
    }
}; ?>

<main>
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('rooms.list_title') }}</h1>
                <p class="mt-1 text-slate-600">{{ __('rooms.list_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" wire:model.live.debounce.800ms="q" class="ui-input w-64" placeholder="{{ __('rooms.search_placeholder') }}" />
                <a href="{{ route('home') }}" class="ui-btn-secondary">{{ __('rooms.back_home') }}</a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->items as $item)
                <div class="rounded-xl border border-sky-100 bg-white shadow-sm overflow-hidden transform-gpu transition duration-300 ease-out hover:scale-[1.02] hover:shadow-md hover:border-sky-200">
                    @if(($item->fotos ?? collect())->isNotEmpty())
                        <div class="h-44 bg-slate-100">
                            <img src="{{ asset('storage/'.$item->fotos->first()->path) }}" alt="{{ $item->nama_kamar }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ asset('storage/login/login reg page.webp') }}'">
                        </div>
                    @else
                        <div class="h-44 bg-slate-100 flex items-center justify-center text-slate-400">{{ __('rooms.no_photo') }}</div>
                    @endif
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-slate-900">{{ $item->nama_kamar }}</h3>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200">{{ $item->tipe_kamar ?: __('rooms.default_type') }}</span>
                            <x-status-badge :status="$item->status" />
                        </div>
                        <p class="mt-2 font-semibold text-slate-900">Rp {{ number_format($item->harga, 0, ',', '.') }}{{ __('rooms.price_suffix') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('kamar.show', $item->id) }}" class="ui-btn-primary">{{ __('rooms.view_detail') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-slate-600">{{ __('rooms.no_rooms') }}</div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $this->items->links() }}
        </div>
    </div>
    </section>

    <x-contact-section />
</main>
