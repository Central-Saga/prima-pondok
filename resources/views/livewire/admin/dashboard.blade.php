<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Livewire\Volt\Component;

new class extends Component {
    public int $kamarCount = 0;
    public int $pemesananCount = 0;
    public int $pembayaranPending = 0;

    public function mount(): void
    {
        $this->kamarCount = Kamar::count();
        $this->pemesananCount = Pemesanan::count();
        $this->pembayaranPending = Pembayaran::where('status', 'pending')->count();
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-slate-600">Ringkasan singkat operasional Pondok Teges.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kamar.create') }}" class="inline-flex items-center rounded-lg bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">Tambah Kamar</a>
            <a href="{{ route('admin.galeri.create') }}" class="inline-flex items-center rounded-lg bg-sky-50 px-4 py-2 text-sm font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-100">Tambah Gambar</a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="rounded-2xl bg-gradient-to-r from-sky-500 to-sky-400 text-white p-6 shadow-sm">
            <div class="text-sm/6 opacity-90">Total Kamar</div>
            <div class="mt-2 text-4xl font-semibold">{{ $kamarCount }}</div>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">
            <div class="text-sm/6 text-slate-600">Total Pemesanan</div>
            <div class="mt-2 text-4xl font-semibold text-slate-900">{{ $pemesananCount }}</div>
        </div>
        <div class="rounded-2xl border border-amber-100 bg-amber-50 p-6 shadow-sm">
            <div class="text-sm/6 text-amber-700">Pembayaran Pending</div>
            <div class="mt-2 text-4xl font-semibold text-amber-800">{{ $pembayaranPending }}</div>
        </div>
    </div>

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.kamar.index') }}" class="rounded-xl border border-sky-100 bg-white p-5 hover:border-sky-200 hover:shadow-sm transition">
            <div class="text-slate-900 font-medium">Kelola Kamar</div>
            <p class="text-slate-600 text-sm mt-1">Tambah, ubah, dan atur status kamar.</p>
        </a>

        <a href="{{ route('admin.galeri.index') }}" class="rounded-xl border border-sky-100 bg-white p-5 hover:border-sky-200 hover:shadow-sm transition">
            <div class="text-slate-900 font-medium">Kelola Galeri</div>
            <p class="text-slate-600 text-sm mt-1">Atur foto untuk landing page.</p>
        </a>

        <a href="{{ route('admin.pembayaran') }}" class="rounded-xl border border-sky-100 bg-white p-5 hover:border-sky-200 hover:shadow-sm transition">
            <div class="text-slate-900 font-medium">Verifikasi Pembayaran</div>
            <p class="text-slate-600 text-sm mt-1">Tinjau bukti dan konfirmasi pembayaran.</p>
        </a>
    </div>
</section>
