<?php

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public Pembayaran $pembayaran;

    public array $pemesananList = [];
    public ?int $pemesanan_id = null;
    public string $metode_bayar = 'transfer';
    public float $jumlah = 0.0;
    public $bukti; // new file
    public string $status = Pembayaran::STATUS_PENDING;
    public ?string $alasan_penolakan = null;

    public function mount(Pembayaran $pembayaran): void
    {
        $this->pembayaran = $pembayaran->load('pemesanan.kamar', 'pemesanan.wisatawan');
        $this->pemesananList = Pemesanan::with(['kamar','wisatawan'])->latest()->take(100)->get()->map(fn($p)=>[
            'id'=>$p->id,
            'label'=> ($p->kamar->nama_kamar ?? '-') . ' - ' . ($p->wisatawan->name ?? '-') . ' (#'.$p->id.')'
        ])->toArray();

        $this->pemesanan_id = $pembayaran->pemesanan_id;
        $this->metode_bayar = $pembayaran->metode_bayar;
        $this->jumlah = (float) $pembayaran->jumlah;
        $this->status = request()->query('status') ?: $pembayaran->status;
        $this->alasan_penolakan = $pembayaran->alasan_penolakan;
    }

    public function save(): void
    {
        $reasonRule = $this->status === Pembayaran::STATUS_REJECTED
            ? 'required|string|max:500'
            : 'nullable|string|max:500';

        $this->validate([
            'pemesanan_id' => 'required|exists:pemesanan,id',
            'metode_bayar' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'status' => 'required|string',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:4096',
            'alasan_penolakan' => $reasonRule,
        ]);

        $data = [
            'pemesanan_id' => $this->pemesanan_id,
            'metode_bayar' => $this->metode_bayar,
            'jumlah' => $this->jumlah,
            'status' => $this->status,
            'alasan_penolakan' => $this->status === Pembayaran::STATUS_REJECTED ? $this->alasan_penolakan : null,
        ];
        if ($this->bukti) {
            $data['bukti_pembayaran'] = str_replace('\\','/',$this->bukti->store('bukti','public'));
        }

        $this->pembayaran->update($data);
        $this->redirectRoute('admin.pembayaran');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit Pembayaran</h1>
        <p class="mt-1 ui-help">Perbarui data pembayaran.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div>
            <label class="ui-label">Pemesanan</label>
            <select wire:model="pemesanan_id" class="ui-select" disabled>
                @foreach($pemesananList as $p)
                    <option value="{{ $p['id'] }}">{{ $p['label'] }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-slate-500">Wisatawan tidak dapat diubah pada pembayaran yang sudah tercatat.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Metode</label>
                <select wire:model="metode_bayar" class="ui-select">
                    <option value="transfer">transfer</option>
                    <option value="tunai">tunai</option>
                </select>
            </div>
            <div>
                <label class="ui-label">Jumlah</label>
                <input type="number" wire:model="jumlah" class="ui-input" />
            </div>
        </div>
        <div>
            <label class="ui-label">Ganti Bukti (opsional)</label>
            <input type="file" wire:model="bukti" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
        </div>
        <div>
            <label class="ui-label">Status</label>
            <select wire:model="status" class="ui-select w-40">
                <option value="pending">pending</option>
                <option value="verified">verified</option>
                <option value="rejected">rejected</option>
            </select>
        </div>
        @if($status === 'rejected')
            <div>
                <label class="ui-label">Alasan reject</label>
                <textarea wire:model="alasan_penolakan" rows="3" class="ui-input" placeholder="Contoh: Bukti transfer tidak jelas / nominal tidak sesuai / rekening tujuan salah."></textarea>
                @error('alasan_penolakan') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
        @endif
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="{{ route('admin.pembayaran') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>
