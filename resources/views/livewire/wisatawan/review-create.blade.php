<?php

use App\Models\Pemesanan;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public Pemesanan $pemesanan;
    public ?Review $review = null;

    public int $rating = 5;
    public string $komentar = '';

    public function mount(Pemesanan $pemesanan): void
    {
        $user = Auth::user();
        $wisatawanId = $user?->wisatawan?->id;
        abort_if(! $wisatawanId || $pemesanan->wisatawan_id !== $wisatawanId, 403);

        $pemesanan->load('kamar');
        $this->pemesanan = $pemesanan;

        $this->review = Review::query()
            ->where('pemesanan_id', $pemesanan->id)
            ->where('wisatawan_id', $wisatawanId)
            ->first();

        if ($this->review) {
            $this->rating = (int) $this->review->rating;
            $this->komentar = (string) $this->review->komentar;
            return;
        }

        $eligibleStatus = in_array($pemesanan->status, [Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_COMPLETED], true);
        abort_if(! $eligibleStatus, 403);
    }

    public function save(): void
    {
        if ($this->review && $this->review->is_published) {
            abort(403);
        }

        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:3|max:2000',
        ]);

        $wisatawanId = Auth::user()?->wisatawan?->id;
        abort_if(! $wisatawanId, 403);

        $this->review = Review::updateOrCreate(
            ['pemesanan_id' => $this->pemesanan->id],
            [
                'wisatawan_id' => $wisatawanId,
                'kamar_id' => $this->pemesanan->kamar_id,
                'rating' => $this->rating,
                'komentar' => $this->komentar,
            ]
        );

        session()->flash('status', __('reviews.flash_saved'));
        $this->redirectRoute('booking.index');
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('reviews.page_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ $pemesanan->kamar->nama_kamar ?? '-' }} • Booking #{{ $pemesanan->id }}
                </p>
            </div>
            <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('reviews.back') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
            @if($review && $review->is_published)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    {{ __('reviews.locked') }}
                </div>
            @else
                <form wire:submit="save" class="space-y-5">
                    <div>
                        <div class="ui-label">{{ __('reviews.rating') }}</div>
                        <div class="mt-2 flex items-center gap-1" role="radiogroup" aria-label="Rating">
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
                        <label class="ui-label">{{ __('reviews.comment') }}</label>
                        <textarea wire:model.live.debounce.300ms="komentar" rows="5" class="ui-input" placeholder="{{ __('reviews.comment_placeholder') }}"></textarea>
                        @error('komentar') <div class="ui-error mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('reviews.cancel') }}</a>
                        <button class="ui-btn-primary">{{ $review ? __('reviews.submit_update') : __('reviews.submit_new') }}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
