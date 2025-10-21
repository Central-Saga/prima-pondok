<?php

use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Livewire\Volt\Component;

new class extends Component {
    public string $from;
    public string $to;

    public int $totalPemesanan = 0;
    public float $totalPendapatan = 0.0;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to = now()->toDateString();
        $this->recalc();
    }

    public function updated($prop): void
    {
        if (in_array($prop, ['from','to'])) {
            $this->recalc();
        }
    }

    private function recalc(): void
    {
        $from = \Carbon\Carbon::parse($this->from)->startOfDay();
        $to = \Carbon\Carbon::parse($this->to)->endOfDay();
        $this->totalPemesanan = Pemesanan::whereBetween('created_at', [$from,$to])->count();
        $this->totalPendapatan = (float) Pembayaran::where('status','verified')
            ->whereBetween('created_at', [$from,$to])
            ->sum('jumlah');
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Laporan</h1>
            <p class="mt-1 ui-help">Ringkasan pemesanan dan pendapatan berdasarkan rentang tanggal.</p>
        </div>
        <div class="flex items-end gap-3">
            <div>
                <label class="ui-label">Dari</label>
                <input type="date" wire:model="from" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Sampai</label>
                <input type="date" wire:model="to" class="ui-input" />
            </div>
            <a href="{{ route('admin.laporan.export', ['from' => $from, 'to' => $to]) }}" class="ui-btn-secondary">Export CSV</a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="ui-card">
            <div class="text-sm text-slate-600">Total Pemesanan</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $totalPemesanan }}</div>
        </div>
        <div class="ui-card">
            <div class="text-sm text-slate-600">Total Pendapatan (Verified)</div>
            <div class="mt-2 text-3xl font-semibold text-slate-900">Rp {{ number_format($totalPendapatan,0,',','.') }}</div>
        </div>
    </div>
</section>

