<?php

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Bank;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    use WithFileUploads;

    public Pemesanan $pemesanan;
    public ?Pembayaran $pembayaranTerakhir = null;
    public ?string $metode_bayar = 'transfer';
    public ?float $jumlah = null;
    public $bukti; // file
    public array $banks = [];
    public string $paymentInfo = '';
    public bool $canUploadProof = true;

    public function mount(Pemesanan $pemesanan): void
    {
        $user = Auth::user();
        $wisatawanId = $user->wisatawan?->id;
        abort_if(!$wisatawanId || $pemesanan->wisatawan_id !== $wisatawanId, 403);
        $this->pemesanan = $pemesanan;
        $this->jumlah = (float) $pemesanan->total_bayar;
        $this->banks = Bank::query()->where('status','active')->orderBy('nama_bank')->get(['nama_bank','no_transfer'])->toArray();

        $this->pembayaranTerakhir = Pembayaran::query()
            ->where('pemesanan_id', $this->pemesanan->id)
            ->latest('id')
            ->first();

        $this->syncPaymentState();
    }

    private function syncPaymentState(): void
    {
        $this->paymentInfo = '';
        $this->canUploadProof = true;

        if (! $this->pembayaranTerakhir) {
            return;
        }

        // Jika status pemesanan confirmed (disetujui admin)
        if ($this->pemesanan->status === Pemesanan::STATUS_CONFIRMED) {
            $this->paymentInfo = __('booking.payment_status_verified');
            $this->canUploadProof = false;
            return;
        }

        // Jika status pemesanan cancelled (dibatalkan admin)
        if ($this->pemesanan->status === Pemesanan::STATUS_CANCELLED) {
            if ($this->pemesanan->cancel_category === Pemesanan::CANCEL_CATEGORY_PAYMENT) {
                $this->paymentInfo = __('booking.payment_status_cancelled_reupload');
                $this->canUploadProof = true;
            } else {
                $this->paymentInfo = __('booking.payment_status_cancelled');
                $this->canUploadProof = false;
            }
            return;
        }

        // Jika status pemesanan pending (menunggu verifikasi admin)
        if ($this->pemesanan->status === Pemesanan::STATUS_PENDING) {
            if ($this->pembayaranTerakhir->status === Pembayaran::STATUS_PENDING) {
                $this->paymentInfo = __('booking.payment_status_pending');
                $this->canUploadProof = false;
                return;
            }
        }

        if ($this->pembayaranTerakhir->status === Pembayaran::STATUS_REJECTED) {
            $this->paymentInfo = __('booking.payment_status_rejected');
            $this->canUploadProof = true;
        }
    }

    public function cancel(): void
    {
        $lastPayment = Pembayaran::query()
            ->where('pemesanan_id', $this->pemesanan->id)
            ->latest('id')
            ->first();

        if ($lastPayment && in_array($lastPayment->status, [Pembayaran::STATUS_PENDING, Pembayaran::STATUS_VERIFIED], true)) {
            $this->addError('bukti', __('booking.payment_cannot_cancel'));
            return;
        }

        if ($this->pemesanan->status === Pemesanan::STATUS_WAITING) {
            $this->pemesanan->delete();
        } elseif ($this->pemesanan->status === Pemesanan::STATUS_PENDING) {
            $this->pemesanan->update(['status' => Pemesanan::STATUS_CANCELLED]);
        }

        $this->redirectRoute('booking.index');
    }

    public function submit(): void
    {
        $this->pembayaranTerakhir = Pembayaran::query()
            ->where('pemesanan_id', $this->pemesanan->id)
            ->latest('id')
            ->first();
        $this->syncPaymentState();

        if (! $this->canUploadProof) {
            $this->addError('bukti', __('booking.payment_cannot_upload'));
            return;
        }

        $this->validate([
            'metode_bayar' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $path = $this->bukti->store('bukti', 'public');
        $path = str_replace('\\', '/', $path);

        if ($this->pembayaranTerakhir && $this->pembayaranTerakhir->status === Pembayaran::STATUS_REJECTED) {
            $this->pembayaranTerakhir->update([
                'metode_bayar' => $this->metode_bayar,
                'jumlah' => $this->jumlah,
                'bukti_pembayaran' => $path,
                'status' => Pembayaran::STATUS_PENDING,
                'alasan_penolakan' => null,
            ]);
        } else {
            Pembayaran::create([
                'pemesanan_id' => $this->pemesanan->id,
                'metode_bayar' => $this->metode_bayar,
                'jumlah' => $this->jumlah,
                'bukti_pembayaran' => $path,
                'status' => Pembayaran::STATUS_PENDING,
                'alasan_penolakan' => null,
            ]);
        }

        if (in_array($this->pemesanan->status, [Pemesanan::STATUS_WAITING, Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CANCELLED], true)) {
            $this->pemesanan->update([
                'status' => Pemesanan::STATUS_PENDING,
                'catatan_cancel' => null,
            ]);
        }

        $this->redirectRoute('booking.show', $this->pemesanan->id);
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Flash message when redirected from blocked extend page --}}
        @if(session('extend_blocked'))
            <div class="mb-6 rounded-xl border-2 border-rose-200 bg-rose-50 p-4 flex items-start gap-3" id="extend-blocked-flash">
                <svg class="h-6 w-6 flex-shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-rose-800">{{ session('extend_blocked') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('extend-blocked-flash').remove()" class="text-rose-400 hover:text-rose-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('booking.detail_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('booking.detail_subtitle') }}</p>
            </div>

        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-slate-600">{{ __('booking.room') }}</div>
                            <div class="mt-0.5 text-lg font-medium text-slate-900">{{ $pemesanan->kamar->nama_kamar }}</div>
                        </div>
                        <x-status-badge :status="$pemesanan->status" />
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">{{ __('booking.checkin') }}</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->tanggal_checkin->format('d M Y') }}</div>
                            <div class="text-xs text-slate-900 mt-1">12:00 PM</div>
                        </div>
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">{{ __('booking.checkout') }}</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</div>
                            <div class="text-xs text-slate-900 mt-1">11:00 AM</div>
                        </div>
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">{{ __('booking.duration') }}</div>
                            <div class="font-medium text-slate-900">
                                {{ $pemesanan->jumlah_hari }}
                                {{ trans_choice('booking.nights', $pemesanan->jumlah_hari) }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 rounded-lg border border-sky-100 bg-sky-50 p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <div class="text-slate-600">{{ __('booking.total') }}</div>
                            <div class="text-lg font-semibold text-slate-900">Rp {{ number_format($pemesanan->total_bayar,0,',','.') }}</div>
                        </div>
                    </div>
                    @if($pemesanan->status === \App\Models\Pemesanan::STATUS_CANCELLED && $pemesanan->catatan_cancel)
                        <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm">
                            <div class="flex items-center gap-2">
                                <div class="font-semibold text-rose-700">{{ __('booking.cancel_note_title') }}</div>
                                @if($pemesanan->cancel_category === \App\Models\Pemesanan::CANCEL_CATEGORY_PAYMENT)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">{{ __('booking.cancel_category_payment') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-700">{{ __('booking.cancel_category_general') }}</span>
                                @endif
                            </div>
                            <div class="mt-1 text-slate-900">{{ $pemesanan->cancel_note }}</div>
                            @if($pemesanan->isCancelledForPayment())
                                <div class="mt-2 text-xs text-rose-700 font-medium">{{ __('booking.cancel_reupload_hint') }}</div>
                            @endif
                        </div>
                    @endif
                    @if($pemesanan->status === \App\Models\Pemesanan::STATUS_WAITING)
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('kamar.show', $pemesanan->kamar->id) }}?reschedule_id={{ $pemesanan->id }}" class="flex-1 inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
                                <svg class="inline-block w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                {{ __('booking.reschedule') }}
                            </a>
                        </div>
                    @endif
                    {{-- Extend button for confirmed bookings near/past checkout --}}
                    @if(in_array($pemesanan->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND]))
                        @if($pemesanan->needsCheckoutAlert() || $pemesanan->isCheckoutOverdue())
                            <div class="mt-4 rounded-lg border-2 {{ $pemesanan->isCheckoutOverdue() ? 'border-rose-200 bg-rose-50' : 'border-amber-200 bg-amber-50' }} p-4">
                                <div class="flex items-start gap-2">
                                    <svg class="w-5 h-5 flex-shrink-0 {{ $pemesanan->isCheckoutOverdue() ? 'text-rose-600' : 'text-amber-600' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <div class="flex-1">
                                        @if($pemesanan->isCheckoutOverdue())
                                            <p class="text-sm font-semibold text-rose-800">{{ __('booking.checkout_overdue_title') }}</p>
                                            <p class="text-xs text-rose-700 mt-1">{{ __('booking.checkout_overdue_denda', ['amount' => number_format(\App\Models\Pemesanan::DENDA_AMOUNT, 0, ',', '.')]) }}</p>
                                        @else
                                            <p class="text-sm font-semibold text-amber-800">{{ __('booking.checkout_approaching_title') }}</p>
                                            <p class="text-xs text-amber-700 mt-1">{{ __('booking.checkout_approaching_deadline', ['date' => $pemesanan->tanggal_checkout->format('d M Y')]) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    @if($pemesanan->canExtend(1))
                                        <a href="{{ route('booking.extend', $pemesanan->id) }}" class="flex-1 inline-flex items-center justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-500 shadow">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('booking.extend_stay') }}
                                        </a>
                                    @else
                                        <button type="button" class="flex-1 inline-flex items-center justify-center rounded-md bg-violet-600 px-4 py-2 text-sm font-semibold text-white hover:bg-violet-500 shadow"
                                                onclick="document.getElementById('extend-unavailable-modal-show').classList.remove('hidden')">
                                            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('booking.extend_stay') }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if($pemesanan->status !== \App\Models\Pemesanan::STATUS_CANCELLED || $pemesanan->isCancelledForPayment())
            <aside>
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('booking.confirm_payment') }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ __('booking.confirm_payment_subtitle') }}</p>

                    <form wire:submit="submit" class="mt-4 space-y-4 text-sm">
                        @if($pembayaranTerakhir || $paymentInfo)
                            <div class="rounded-lg border p-3 text-sm
                                {{ $pemesanan->status === \App\Models\Pemesanan::STATUS_CONFIRMED ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : '' }}
                                {{ $pemesanan->status === \App\Models\Pemesanan::STATUS_PENDING ? 'border-amber-200 bg-amber-50 text-amber-800' : '' }}
                                {{ $pemesanan->status === \App\Models\Pemesanan::STATUS_CANCELLED ? 'border-rose-200 bg-rose-50 text-rose-800' : '' }}
                            ">
                                <div class="font-medium">{{ $paymentInfo }}</div>
                                @if($pembayaranTerakhir && $pembayaranTerakhir->status === 'rejected' && filled($pembayaranTerakhir->alasan_penolakan))
                                    <div class="mt-2">
                                        <div class="text-xs font-semibold text-rose-900">{{ __('booking.payment_reject_reason_label') }}</div>
                                        <div class="mt-1 whitespace-pre-line text-sm">{{ $pembayaranTerakhir->alasan_penolakan }}</div>
                                    </div>
                                    <div class="mt-2 text-xs text-rose-700">{{ __('booking.payment_reupload_note') }}</div>
                                @endif
                            </div>
                        @endif

                        @if($canUploadProof)
                            <div>
                                <label class="ui-label">{{ __('booking.method') }}</label>
                                <select wire:model="metode_bayar" class="ui-select">
                                    <option value="transfer">{{ __('booking.bank_transfer') }}</option>
                                    <option value="tunai">{{ __('booking.cash') }}</option>
                                </select>
                            </div>
                            @if($metode_bayar === 'transfer')
                                <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-3">
                                    <div class="text-sm font-medium text-emerald-900">{{ __('booking.destination_account') }}</div>
                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                        @forelse($banks as $b)
                                            <div class="rounded-md bg-white border border-emerald-100 px-3 py-2">
                                                <div class="text-emerald-700">{{ $b['nama_bank'] }}</div>
                                                <div class="font-mono text-slate-900">{{ $b['no_transfer'] }}</div>
                                            </div>
                                        @empty
                                            <div class="text-slate-600">{{ __('booking.no_bank_data') }}</div>
                                        @endforelse
                                    </div>
                                    <div class="mt-2 text-xs text-emerald-700">{{ __('booking.bank_note') }}</div>
                                </div>
                            @endif
                            <input type="hidden" wire:model="jumlah" />
                            <div>
                                <label class="ui-label">{{ __('booking.payment_proof') }}</label>
                                <input type="file" wire:model="bukti" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
                                @error('bukti') <div class="ui-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="pt-1 flex flex-col sm:flex-row sm:items-center sm:gap-3">
                                <button class="ui-btn-primary w-full sm:w-auto">{{ __('booking.submit_proof') }}</button>
                                <button type="button" wire:click="cancel" class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                    {{ __('booking.cancel') }}
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </aside>
            @endif
        </div>
    </div>

    {{-- MODAL: Extend tidak tersedia --}}
    <div id="extend-unavailable-modal-show" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/50" onclick="this.parentElement.classList.add('hidden')"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
                <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('booking.extend_unavailable_title') }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ __('booking.extend_unavailable_message') }}</p>
            <button type="button" onclick="this.closest('#extend-unavailable-modal-show').classList.add('hidden')" class="mt-5 w-full rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-500">{{ __('booking.extend_unavailable_ok') }}</button>
        </div>
    </div>
</section>
