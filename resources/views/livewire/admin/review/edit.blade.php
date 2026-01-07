<?php

use App\Models\Review;
use Livewire\Volt\Component;

new class extends Component {
    public Review $review;

    public int $rating = 5;
    public string $komentar = '';
    public bool $is_published = false;

    public function mount(Review $review): void
    {
        $this->review = $review->load(['pemesanan','kamar','wisatawan']);
        $this->rating = (int) $review->rating;
        $this->komentar = (string) $review->komentar;
        $this->is_published = (bool) $review->is_published;
    }

    public function save(): void
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:3|max:2000',
            'is_published' => 'boolean',
        ]);

        $this->review->update([
            'rating' => $this->rating,
            'komentar' => $this->komentar,
            'is_published' => $this->is_published,
        ]);

        $this->redirectRoute('admin.review.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Edit Review</h1>
            <p class="mt-1 text-slate-600 text-sm">
                Booking #{{ $review->pemesanan_id }} • {{ $review->kamar->nama_kamar ?? '-' }} • {{ $review->wisatawan->name ?? '-' }}
            </p>
        </div>
        <a href="{{ route('admin.review.index') }}" class="ui-btn-secondary">Kembali</a>
    </div>

    <div class="mt-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
        <form wire:submit="save" class="space-y-4 text-sm">
            <div>
                <div class="ui-label">Rating</div>
                <div class="mt-2 flex items-center gap-1">
                    @for($i=1; $i<=5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" class="sr-only" wire:model.live="rating" value="{{ $i }}">
                            <svg class="h-7 w-7 {{ $rating >= $i ? 'text-amber-400' : 'text-slate-200' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.71c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                            </svg>
                        </label>
                    @endfor
                </div>
                @error('rating') <div class="ui-error mt-1">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="ui-label">Komentar</label>
                <textarea wire:model.live.debounce.300ms="komentar" rows="5" class="ui-input"></textarea>
                @error('komentar') <div class="ui-error mt-1">{{ $message }}</div> @enderror
            </div>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                <span>Tampilkan di landing page</span>
            </label>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ route('admin.review.index') }}" class="ui-btn-secondary">Batal</a>
                <button class="ui-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</section>
