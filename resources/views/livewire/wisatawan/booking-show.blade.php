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

        if (
            $this->pembayaranTerakhir
            && $this->pemesanan->status === Pemesanan::STATUS_CANCELLED
            && in_array($this->pembayaranTerakhir->status, [Pembayaran::STATUS_PENDING, Pembayaran::STATUS_REJECTED], true)
        ) {
            $this->pemesanan->update(['status' => Pemesanan::STATUS_PENDING]);
        }

        $this->syncPaymentState();
    }

    private function syncPaymentState(): void
    {
        $this->paymentInfo = '';
        $this->canUploadProof = true;

        if (! $this->pembayaranTerakhir) {
            return;
        }

        if ($this->pembayaranTerakhir->status === Pembayaran::STATUS_VERIFIED) {
            $this->paymentInfo = __('booking.payment_status_verified');
            $this->canUploadProof = false;
            return;
        }

        if ($this->pembayaranTerakhir->status === Pembayaran::STATUS_PENDING) {
            $this->paymentInfo = __('booking.payment_status_pending');
            $this->canUploadProof = false;
            return;
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

        if ($this->pemesanan->status === Pemesanan::STATUS_PENDING) {
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

        if (in_array($this->pemesanan->status, [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CANCELLED], true)) {
            $this->pemesanan->update(['status' => Pemesanan::STATUS_PENDING]);
        }

        $this->redirectRoute('booking.show', $this->pemesanan->id);
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('booking.detail_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('booking.detail_subtitle') }}</p>
            </div>
            <a
                href="{{ route('booking.index') }}"
                class="hidden sm:inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50"
            >
                {{ __('booking.back') }}
            </a>
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
                        </div>
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">{{ __('booking.checkout') }}</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</div>
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
                </div>
            </div>

            <aside>
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">{{ __('booking.confirm_payment') }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ __('booking.confirm_payment_subtitle') }}</p>

                    <form wire:submit="submit" class="mt-4 space-y-4 text-sm">
                        @if($pembayaranTerakhir)
                            <div class="rounded-lg border p-3 text-sm
                                {{ $pembayaranTerakhir->status === 'verified' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : '' }}
                                {{ $pembayaranTerakhir->status === 'pending' ? 'border-amber-200 bg-amber-50 text-amber-800' : '' }}
                                {{ $pembayaranTerakhir->status === 'rejected' ? 'border-rose-200 bg-rose-50 text-rose-800' : '' }}
                            ">
                                <div class="font-medium">{{ $paymentInfo }}</div>
                                @if($pembayaranTerakhir->status === 'rejected' && filled($pembayaranTerakhir->alasan_penolakan))
                                    <div class="mt-2">
                                        <div class="text-xs font-semibold text-rose-900">{{ __('booking.payment_reject_reason_label') }}</div>
                                        <div class="mt-1 whitespace-pre-line text-sm">{{ $pembayaranTerakhir->alasan_penolakan }}</div>
                                    </div>
                                    <div class="mt-2 text-xs text-rose-700">{{ __('booking.payment_reupload_note') }}</div>
                                @endif
                            </div>
                        @endif

                        <div>
                            <label class="ui-label">{{ __('booking.method') }}</label>
                            <select wire:model="metode_bayar" class="ui-select" @disabled(! $canUploadProof)>
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
                            <input type="file" wire:model="bukti" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" @disabled(! $canUploadProof) />
                            @error('bukti') <div class="ui-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="pt-1 flex flex-col sm:flex-row sm:items-center sm:gap-3">
                            <button class="ui-btn-primary w-full sm:w-auto disabled:opacity-60 disabled:cursor-not-allowed" @disabled(! $canUploadProof)>{{ __('booking.submit_proof') }}</button>
                            <button type="button" wire:click="cancel" class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 disabled:opacity-60 disabled:cursor-not-allowed" @disabled(! $canUploadProof)>
                                {{ __('booking.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>
