<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $status = 'all';
    public string $q = '';
    public ?int $checkoutId = null;
    public ?int $detailId = null;
    public ?Pemesanan $detail = null;
    public string $detailStatus = Pemesanan::STATUS_PENDING;
    public ?string $detailNote = null;
    public string $detailCancelCategory = 'general';
    public ?int $highlightId = null;

    public function mount(): void
    {
        $this->highlightId = request()->query('highlight');
    }

    public function updatingQ(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return Pemesanan::with(['kamar','wisatawan'])
            ->where('status', '!=', Pemesanan::STATUS_WAITING)
            ->when($this->status !== 'all', fn($q) => $q->where('status',$this->status))
            ->when($this->q !== '', function($q){
                $term = '%'.$this->q.'%';
                $q->whereHas('wisatawan', fn($w) => $w->where('name','like',$term))
                  ->orWhereHas('kamar', fn($k) => $k->where('nama_kamar','like',$term))
                  ->orWhere('id', $this->q);
            })
            ->latest()
            ->paginate(10);
    }

    public function delete(int $id): void
    {
        Pemesanan::whereKey($id)->delete();
        $this->resetPage();
    }

    public function checkout(int $id): void
    {
        $booking = Pemesanan::find($id);
        if (! $booking) {
            return;
        }

        if (!in_array($booking->status, [Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_EXTEND])) {
            return;
        }

        $booking->update(['status' => Pemesanan::STATUS_COMPLETED]);
        $this->resetPage();
    }

    public function confirmCheckout(int $id): void
    {
        $this->checkoutId = $id;
    }

    public function cancelCheckout(): void
    {
        $this->checkoutId = null;
    }

    public function doCheckout(): void
    {
        if (! $this->checkoutId) {
            return;
        }

        $this->checkout($this->checkoutId);
        $this->checkoutId = null;
    }

    public function openDetail(int $id): void
    {
        $this->detail = Pemesanan::with(['kamar','wisatawan','pembayaran'])->find($id);
        if (! $this->detail) {
            return;
        }
        $this->detailId = $id;
        $this->detailStatus = $this->detail->status;
        $this->detailNote = $this->detail->cancel_note;
        $this->detailCancelCategory = $this->detail->cancel_category;
    }

    public function closeDetail(): void
    {
        $this->detailId = null;
        $this->detail = null;
        $this->detailStatus = Pemesanan::STATUS_PENDING;
        $this->detailNote = null;
        $this->detailCancelCategory = 'general';
    }

    public function saveDetail(): void
    {
        if (! $this->detailId) {
            return;
        }

        $this->validate([
            'detailStatus' => 'required|in:pending,confirmed,cancelled',
            'detailNote' => 'nullable|string',
        ]);

        if ($this->detailStatus === Pemesanan::STATUS_CANCELLED) {
            $this->validate([
                'detailNote' => 'required|string|min:3',
                'detailCancelCategory' => 'required|in:bukti_pembayaran,general',
            ]);
        }

        $isCancelled = $this->detailStatus === Pemesanan::STATUS_CANCELLED;

        Pemesanan::whereKey($this->detailId)->update([
            'status' => $this->detailStatus,
            'catatan_cancel' => $isCancelled
                ? Pemesanan::encodeCancelNote($this->detailCancelCategory, $this->detailNote)
                : null,
        ]);

        $this->closeDetail();
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pemesanan</h1>
            <p class="mt-1 ui-help">Daftar pemesanan terbaru. Gunakan filter status untuk menyaring.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari tamu/kamar/#id" class="ui-input w-56" />
            <select wire:model.live.debounce.1000ms="status" class="ui-select">
                <option value="all">Semua</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
                <option value="extend">Extend</option>
            </select>
            <a href="{{ route('admin.pemesanan.create') }}" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Kode</th>
                    <th class="py-2 px-3 text-left">Kamar</th>
                    <th class="py-2 px-3 text-left">Wisatawan</th>
                    <th class="py-2 px-3 text-left">Check-in</th>
                    <th class="py-2 px-3 text-left">Check-out</th>
                    <th class="py-2 px-3 text-left">Total</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3 text-left">Checkout</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $row)
                <tr class="{{ $row->id === $highlightId ? 'bg-sky-100 ring-2 ring-sky-400' : '' }}">
                    <td class="py-2 px-3">#{{ $row->id }}</td>
                    <td class="py-2 px-3">{{ $row->kamar->nama_kamar }}</td>
                    <td class="py-2 px-3">{{ $row->wisatawan->name ?? '-' }}</td>
                    <td class="py-2 px-3">{{ $row->tanggal_checkin->format('d M Y') }}</td>
                    <td class="py-2 px-3">{{ $row->tanggal_checkout->format('d M Y') }}</td>
                    <td class="py-2 px-3">Rp {{ number_format($row->total_bayar,0,',','.') }}</td>
                    <td class="py-2 px-3">
                        <x-status-badge :status="$row->status" />
                        @if($row->is_extend)
                            <span class="ml-1 inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">EXT</span>
                        @endif
                    </td>
                    <td class="py-2 px-3">
                        @if(in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                            @php $coStatus = $row->checkout_status; @endphp
                            @if($coStatus === 'diperingati')
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 animate-pulse">
                                    ⚠ Diperingati
                                </span>
                            @elseif($coStatus === 'jatuh_tempo')
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 animate-pulse">
                                    🔴 Jatuh Tempo
                                </span>
                            @elseif($coStatus === 'extend')
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">
                                    🔄 Extend
                                </span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        @else
                            <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="py-2 px-3 text-right">
                        <button type="button" wire:click="openDetail({{ $row->id }})" class="ui-btn-secondary">Detail</button>
                        @if(in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                            <button wire:click="confirmCheckout({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Check-out</button>
                        @endif
                        @if(!in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_COMPLETED, \App\Models\Pemesanan::STATUS_EXTEND]))
                        <button onclick="if(!confirm('Hapus pemesanan ini?')){event.stopImmediatePropagation()}" wire:click="delete({{ $row->id }})" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
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

    @if($checkoutId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" wire:click="cancelCheckout" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
            <div role="dialog" aria-modal="true" class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Konfirmasi Check-out</div>
                <p class="mt-1 text-sm text-slate-600">Tandai booking ini sebagai <span class="font-medium">completed</span> (check-out)?</p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" wire:click="cancelCheckout" class="ui-btn-secondary">Batal</button>
                    <button type="button" wire:click="doCheckout" class="ui-btn-primary !bg-emerald-600 hover:!bg-emerald-500">Ya, Check-out</button>
                </div>
            </div>
        </div>
    @endif

    @if($detailId && $detail)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" wire:click="closeDetail" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
            <div role="dialog" aria-modal="true" class="relative w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Detail Pemesanan #{{ $detail->id }}</h2>
                        <p class="mt-1 text-sm text-slate-600">Kelola status dan lihat informasi lengkap pemesanan.</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 rounded-2xl border border-sky-100 bg-white p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-slate-500">Kamar</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ $detail->kamar->nama_kamar }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Wisatawan</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ $detail->wisatawan->name ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Check-in</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ $detail->tanggal_checkin->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Check-out</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ $detail->tanggal_checkout->format('d M Y') }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Jumlah Malam</div>
                                <div class="mt-1 font-semibold text-slate-900">{{ $detail->jumlah_hari }}</div>
                            </div>
                            <div>
                                <div class="text-slate-500">Total Bayar</div>
                                <div class="mt-1 font-semibold text-slate-900">Rp {{ number_format($detail->total_bayar,0,',','.') }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-status-badge :status="$detail->status" />
                            @if($detail->is_extend)
                                <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">
                                    Extend dari #{{ $detail->extend_from_id }}
                                </span>
                            @endif
                            @if(in_array($detail->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                                @php $coStatus = $detail->checkout_status; @endphp
                                @if($coStatus === 'diperingati')
                                    <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 animate-pulse">⚠ Diperingati</span>
                                @elseif($coStatus === 'jatuh_tempo')
                                    <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 animate-pulse">🔴 Jatuh Tempo</span>
                                @endif
                            @endif
                        </div>
                        @php
                            $latestPayment = ($detail->pembayaran ?? collect())->sortByDesc('id')->first();
                            $proof = $latestPayment?->bukti_pembayaran;
                            $proofUrl = null;
                            if ($proof) {
                                $proofUrl = \Illuminate\Support\Str::startsWith($proof, ['http://','https://'])
                                    ? $proof
                                    : url('media/'.$proof);
                            }
                            $lowerProof = $proof ? strtolower($proof) : '';
                            $isImage = $proof && \Illuminate\Support\Str::endsWith($lowerProof, ['.jpg','.jpeg','.png','.gif','.webp']);
                        @endphp
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
                            <div class="font-medium text-slate-900">Bukti Pembayaran</div>
                            @if($proofUrl)
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <div class="text-slate-600">{{ $latestPayment?->metode_bayar ?? '-' }} • {{ $latestPayment?->status ?? '-' }}</div>
                                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="text-sky-700 hover:underline">Lihat File</a>
                                </div>
                                @if($isImage)
                                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener" class="mt-3 block">
                                        <img src="{{ $proofUrl }}" alt="Bukti pembayaran" class="w-full max-h-72 rounded-lg border object-contain bg-white" />
                                    </a>
                                @endif
                            @else
                                <div class="mt-2 text-slate-600">Belum ada bukti pembayaran.</div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-100 bg-white p-5">
                        <h3 class="text-base font-semibold text-slate-900">Aksi</h3>
                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <label class="ui-label">Status</label>
                                <select wire:model.live="detailStatus" class="ui-select">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            @if($detailStatus === \App\Models\Pemesanan::STATUS_CANCELLED)
                                <div>
                                    <label class="ui-label">Kategori Pembatalan <span class="text-rose-600">*</span></label>
                                    <select wire:model.live="detailCancelCategory" class="ui-select">
                                        <option value="bukti_pembayaran">Bukti Pembayaran</option>
                                        <option value="general">General</option>
                                    </select>
                                    @error('detailCancelCategory') <div class="ui-error">{{ $message }}</div> @enderror
                                </div>
                                <div>
                                    <label class="ui-label">Catatan Pembatalan <span class="text-rose-600">*</span></label>
                                    <textarea wire:model.live="detailNote" rows="4" class="ui-input" placeholder="Tulis alasan pembatalan..."></textarea>
                                    @error('detailNote') <div class="ui-error">{{ $message }}</div> @enderror
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeDetail" class="ui-btn-secondary">Kembali</button>
                            <button type="button" wire:click="saveDetail" class="ui-btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
