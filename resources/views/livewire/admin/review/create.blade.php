<?php

use App\Models\Pemesanan;
use App\Models\Review;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $pemesanan_id = null;
    public int $rating = 5;
    public string $komentar = '';
    public bool $is_published = false;

    public ?array $bookingInfo = null;

    public function updatedPemesananId(): void
    {
        $this->bookingInfo = null;
        if (! $this->pemesanan_id) return;
        $p = Pemesanan::with(['kamar','wisatawan'])->find($this->pemesanan_id);
        if (! $p) return;
        $this->bookingInfo = [
            'kamar' => $p->kamar->nama_kamar ?? '-',
            'wisatawan' => $p->wisatawan->name ?? '-',
            'status' => $p->status,
            'checkin' => optional($p->tanggal_checkin)?->format('d M Y'),
            'checkout' => optional($p->tanggal_checkout)?->format('d M Y'),
        ];
    }

    public function save(): void
    {
        $this->validate([
            'pemesanan_id' => 'required|integer|exists:pemesanan,id|unique:reviews,pemesanan_id',
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:3|max:2000',
            'is_published' => 'boolean',
        ]);

        $p = Pemesanan::with(['kamar','wisatawan'])->findOrFail($this->pemesanan_id);

        Review::create([
            'pemesanan_id' => $p->id,
            'wisatawan_id' => $p->wisatawan_id,
            'kamar_id' => $p->kamar_id,
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
            <h1 class="text-2xl font-semibold text-slate-900">Tambah Review</h1>
            <p class="mt-1 text-slate-600 text-sm">Buat review manual untuk booking tertentu.</p>
        </div>
        <a href="{{ route('admin.review.index') }}" class="ui-btn-secondary">Kembali</a>
    </div>

    <div class="mt-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
        <form wire:submit="save" class="space-y-4 text-sm">
            <div>
                <label class="ui-label">ID Booking (Pemesanan)</label>
                <input type="number" wire:model.live.debounce.400ms="pemesanan_id" class="ui-input" placeholder="Contoh: 12" />
                @error('pemesanan_id') <div class="ui-error">{{ $message }}</div> @enderror
            </div>

            @if($bookingInfo)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid sm:grid-cols-2 gap-2">
                        <div><span class="text-slate-600">Kamar:</span> <span class="font-medium text-slate-900">{{ $bookingInfo['kamar'] }}</span></div>
                        <div><span class="text-slate-600">Pelanggan:</span> <span class="font-medium text-slate-900">{{ $bookingInfo['wisatawan'] }}</span></div>
                        <div><span class="text-slate-600">Check-in:</span> <span class="font-medium text-slate-900">{{ $bookingInfo['checkin'] ?? '-' }}</span></div>
                        <div><span class="text-slate-600">Check-out:</span> <span class="font-medium text-slate-900">{{ $bookingInfo['checkout'] ?? '-' }}</span></div>
                    </div>
                    <div class="mt-2 text-xs text-slate-600">Status booking: <span class="capitalize">{{ $bookingInfo['status'] }}</span></div>
                </div>
            @endif

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
                <textarea wire:model.live.debounce.300ms="komentar" rows="5" class="ui-input" placeholder="Isi komentar..."></textarea>
                @error('komentar') <div class="ui-error">{{ $message }}</div> @enderror
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
