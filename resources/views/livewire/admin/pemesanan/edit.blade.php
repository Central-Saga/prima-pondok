<?php

use App\Models\Pemesanan;
use App\Models\Kamar;
use App\Models\Wisatawan;
use Livewire\Volt\Component;

new class extends Component {
    public Pemesanan $pemesanan;

    public array $wisatawanList = [];
    public array $kamarList = [];

    public ?int $wisatawan_id = null;
    public ?int $kamar_id = null;
    public ?string $tanggal_checkin = null;
    public ?string $tanggal_checkout = null;
    public int $jumlah_hari = 1;
    public float $total_bayar = 0.0;
    public string $status = Pemesanan::STATUS_PENDING;

    public function mount(Pemesanan $pemesanan): void
    {
        $this->pemesanan = $pemesanan;
        $this->wisatawanList = Wisatawan::orderBy('name')->get()->toArray();
        $this->kamarList = Kamar::orderBy('nama_kamar')->get()->toArray();

        $this->wisatawan_id = $pemesanan->wisatawan_id;
        $this->kamar_id = $pemesanan->kamar_id;
        $this->tanggal_checkin = optional($pemesanan->tanggal_checkin)?->format('Y-m-d');
        $this->tanggal_checkout = optional($pemesanan->tanggal_checkout)?->format('Y-m-d');
        $this->jumlah_hari = $pemesanan->jumlah_hari;
        $this->total_bayar = (float) $pemesanan->total_bayar;
        $this->status = $pemesanan->status;
    }

    public function updated($field): void
    {
        if (in_array($field, ['tanggal_checkin','tanggal_checkout','kamar_id'])) {
            $this->recalc();
        }
    }

    private function recalc(): void
    {
        $this->jumlah_hari = 1;
        $this->total_bayar = 0;
        if (! $this->kamar_id || ! $this->tanggal_checkin || ! $this->tanggal_checkout) return;
        $start = \Carbon\Carbon::parse($this->tanggal_checkin)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal_checkout)->startOfDay();
        if ($end->lte($start)) return;
        $this->jumlah_hari = max(1, $start->diffInDays($end));
        $kamar = Kamar::find($this->kamar_id);
        if ($kamar) $this->total_bayar = (float) ($kamar->harga * $this->jumlah_hari);
    }

    public function save(): void
    {
        $data = $this->validate([
            'wisatawan_id' => 'required|exists:wisatawan,id',
            'kamar_id' => 'required|exists:kamar,id',
            'tanggal_checkin' => 'required|date',
            'tanggal_checkout' => 'required|date|after:tanggal_checkin',
            'jumlah_hari' => 'required|integer|min:1',
            'total_bayar' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $this->pemesanan->update($data);
        $this->redirectRoute('admin.pemesanan.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit Pemesanan</h1>
        <p class="mt-1 ui-help">Perbarui data pemesanan.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Wisatawan</label>
                <select wire:model="wisatawan_id" class="ui-select">
                    @foreach($wisatawanList as $w)
                        <option value="{{ $w['id'] }}">{{ $w['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="ui-label">Kamar</label>
                <select wire:model="kamar_id" class="ui-select">
                    @foreach($kamarList as $k)
                        <option value="{{ $k['id'] }}">{{ $k['nama_kamar'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Check-in</label>
                <input type="date" wire:model.live.debounce.500ms="tanggal_checkin" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Check-out</label>
                <input type="date" wire:model.live.debounce.500ms="tanggal_checkout" class="ui-input" />
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="ui-label">Jumlah Malam</label>
                <input type="number" wire:model="jumlah_hari" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Total Bayar</label>
                <input type="number" wire:model="total_bayar" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Status</label>
                <select wire:model="status" class="ui-select">
                    <option value="pending">pending</option>
                    <option value="confirmed">confirmed</option>
                    <option value="cancelled">cancelled</option>
                    <option value="completed">completed</option>
                    <option value="extend">extend</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="{{ route('admin.pemesanan.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>

