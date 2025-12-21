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
    public ?string $metode_bayar = 'transfer';
    public ?float $jumlah = null;
    public $bukti; // file
    public array $banks = [];

    public function mount(Pemesanan $pemesanan): void
    {
        $user = Auth::user();
        $wisatawanId = $user->wisatawan?->id;
        abort_if(!$wisatawanId || $pemesanan->wisatawan_id !== $wisatawanId, 403);
        $this->pemesanan = $pemesanan;
        $this->jumlah = (float) $pemesanan->total_bayar;
        $this->banks = Bank::query()->where('status','active')->orderBy('nama_bank')->get(['nama_bank','no_transfer'])->toArray();
    }

    public function cancel(): void
    {
        if ($this->pemesanan->status === Pemesanan::STATUS_PENDING) {
            $this->pemesanan->update(['status' => Pemesanan::STATUS_CANCELLED]);
        }

        $this->redirectRoute('booking.index');
    }

    public function submit(): void
    {
        $this->validate([
            'metode_bayar' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $path = $this->bukti->store('bukti', 'public');
        $path = str_replace('\\', '/', $path);

        Pembayaran::create([
            'pemesanan_id' => $this->pemesanan->id,
            'metode_bayar' => $this->metode_bayar,
            'jumlah' => $this->jumlah,
            'bukti_pembayaran' => $path,
            'status' => Pembayaran::STATUS_PENDING,
        ]);

        $this->redirectRoute('home');
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">Detail Pemesanan</h1>
                <p class="mt-1 text-sm text-slate-600">Periksa ringkasan pemesanan dan unggah bukti pembayaran.</p>
            </div>
            <button
                type="button"
                wire:click="cancel"
                class="hidden sm:inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50"
            >
                Kembali
            </button>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm text-slate-600">Kamar</div>
                            <div class="mt-0.5 text-lg font-medium text-slate-900">{{ $pemesanan->kamar->nama_kamar }}</div>
                        </div>
                        <x-status-badge :status="$pemesanan->status" />
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">Check-in</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->tanggal_checkin->format('d M Y') }}</div>
                        </div>
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">Check-out</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</div>
                        </div>
                        <div class="rounded-lg border border-sky-100 bg-sky-50 p-3">
                            <div class="text-slate-600">Durasi</div>
                            <div class="font-medium text-slate-900">{{ $pemesanan->jumlah_hari }} malam</div>
                        </div>
                    </div>
                    <div class="mt-4 rounded-lg border border-sky-100 bg-sky-50 p-3 text-sm">
                        <div class="flex items-center justify-between">
                            <div class="text-slate-600">Total Bayar</div>
                            <div class="text-lg font-semibold text-slate-900">Rp {{ number_format($pemesanan->total_bayar,0,',','.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <aside>
                <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Konfirmasi Pembayaran</h3>
                    <p class="mt-1 text-sm text-slate-600">Unggah bukti pembayaran untuk mempercepat proses verifikasi.</p>

                    <form wire:submit="submit" class="mt-4 space-y-4 text-sm">
                        <div>
                            <label class="ui-label">Metode</label>
                            <select wire:model="metode_bayar" class="ui-select">
                                <option value="transfer">Transfer Bank</option>
                                <option value="tunai">Tunai</option>
                            </select>
                        </div>
                        @if($metode_bayar === 'transfer')
                            <div class="rounded-lg border border-emerald-100 bg-emerald-50 p-3">
                                <div class="text-sm font-medium text-emerald-900">Rekening Tujuan</div>
                                <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                                    @forelse($banks as $b)
                                        <div class="rounded-md bg-white border border-emerald-100 px-3 py-2">
                                            <div class="text-emerald-700">{{ $b['nama_bank'] }}</div>
                                            <div class="font-mono text-slate-900">{{ $b['no_transfer'] }}</div>
                                        </div>
                                    @empty
                                        <div class="text-slate-600">Belum ada data bank.</div>
                                    @endforelse
                                </div>
                                <div class="mt-2 text-xs text-emerald-700">Silakan transfer sesuai total tagihan, kemudian unggah bukti pembayaran.</div>
                            </div>
                        @endif
                        <input type="hidden" wire:model="jumlah" />
                        <div>
                            <label class="ui-label">Bukti Pembayaran</label>
                            <input type="file" wire:model="bukti" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
                            @error('bukti') <div class="ui-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="pt-1 flex flex-col sm:flex-row sm:items-center sm:gap-3">
                            <button class="ui-btn-primary w-full sm:w-auto">Kirim Bukti</button>
                            <button type="button" wire:click="cancel" class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</section>

