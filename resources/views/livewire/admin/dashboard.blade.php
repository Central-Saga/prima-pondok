<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Livewire\Volt\Component;

new class extends Component {
    public int $kamarCount = 0;
    public int $pemesananCount = 0;
    public int $pembayaranPending = 0; // kept for possible future use
    public float $totalRevenue = 0.0;
    public int $month = 0;
    public int $year = 0;
    public array $monthOptions = [];
    public array $yearOptions = [];

    // Chart data (last 30 days)
    public array $labels = [];
    public array $bookingsSeries = [];
    public array $revenueSeries = [];
    public array $statusCounts = [];

    public function mount(): void
    {
        $now = now();
        $this->month = (int) $now->month;
        $this->year = (int) $now->year;

        $this->monthOptions = [
            1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
            7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
        ];
        $startYear = $now->year - 2;
        $this->yearOptions = range($startYear, $now->year + 1);

        $this->kamarCount = Kamar::count();
        $this->refreshData();
    }

    public function updated($field): void
    {
        if (in_array($field, ['month','year'])) {
            $this->refreshData();
        }
    }

    private function refreshData(): void
    {
        $start = \Carbon\Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // KPI for selected month
        $this->pemesananCount = Pemesanan::whereBetween('created_at', [$start, $end])->count();
        $this->totalRevenue = (float) Pembayaran::where('status','verified')
            ->whereBetween('created_at', [$start, $end])->sum('jumlah');
        $this->pembayaranPending = Pembayaran::where('status','pending')
            ->whereBetween('created_at', [$start, $end])->count();

        // Labels days in month
        $days = collect();
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) { $days->push($d->copy()); }
        $this->labels = $days->map(fn($d) => $d->format('d M'))->toArray();

        $bookingRaw = Pemesanan::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c','d');

        $revenueRaw = Pembayaran::where('status','verified')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as d, SUM(jumlah) as s')
            ->groupBy('d')
            ->pluck('s','d');

        $this->bookingsSeries = $days->map(fn($d) => (int) ($bookingRaw[$d->format('Y-m-d')] ?? 0))->toArray();
        $this->revenueSeries = $days->map(fn($d) => (float) ($revenueRaw[$d->format('Y-m-d')] ?? 0))->toArray();

        $this->statusCounts = Pemesanan::whereBetween('created_at', [$start, $end])
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c','status')
            ->toArray();

        // Dispatch an event so the frontend updates charts when month/year changes
        $this->dispatch('dashboard-data-updated', [
            'labels'   => $this->labels,
            'bookings' => $this->bookingsSeries,
            'revenue'  => $this->revenueSeries,
            'status'   => $this->statusCounts,
            'period'   => ($this->monthOptions[$this->month] ?? (string) $this->month) . ' ' . $this->year,
        ]);
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-slate-600">Ringkasan singkat operasional Pondok Teges.</p>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live.debounce.1000ms="month" class="ui-select w-auto min-w-[150px]">
                @foreach($monthOptions as $mVal => $mName)
                    <option value="{{ $mVal }}">{{ $mName }}</option>
                @endforeach
            </select>
            <select wire:model.live.debounce.1000ms="year" class="ui-select">
                @foreach($yearOptions as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.laporan.export', ['from' => \Carbon\Carbon::create($year,$month,1)->startOfMonth()->format('Y-m-d'), 'to' => \Carbon\Carbon::create($year,$month,1)->endOfMonth()->format('Y-m-d')]) }}" class="inline-flex items-center justify-center rounded-md h-9 px-4 text-sm font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50 whitespace-nowrap min-w-[120px]">Export CSV</a>
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
        <div class="rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-400 text-white p-6 shadow-sm">
            <div class="text-sm/6 opacity-90">Total Pendapatan</div>
            <div class="mt-2 text-3xl sm:text-4xl font-semibold">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="text-slate-900 font-medium mb-3">Aksi Cepat</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('admin.kamar.create') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 hover:bg-sky-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200">
                    <!-- Heroicon: plus-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Tambah Kamar</div>
                    <div class="text-xs text-slate-500">Buat entri kamar baru</div>
                </div>
            </a>

            <a href="{{ route('admin.pemesanan.create') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-emerald-300 hover:bg-emerald-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    <!-- Heroicon: calendar-days -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 7.5v11.25A2.25 2.25 0 006.75 21h10.5A2.25 2.25 0 0019.5 18.75V7.5M8.25 12h.008v.008H8.25V12zm3 0h.008v.008H11.25V12zm3 0h.008v.008H14.25V12z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Buat Pemesanan</div>
                    <div class="text-xs text-slate-500">Input booking manual</div>
                </div>
            </a>

            <a href="{{ route('admin.pemesanan.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300 hover:bg-slate-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200">
                    <!-- Heroicon: list-bullet -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75zm0 5.25h.008v.008H3.75V12zm0 5.25h.008v.008H3.75v-.008z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Daftar Booking</div>
                    <div class="text-xs text-slate-500">Pantau semua pemesanan</div>
                </div>
            </a>

            <a href="{{ route('admin.pembayaran') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-amber-300 hover:bg-amber-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-200">
                    <!-- Heroicon: banknotes -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 18.75h19.5M3 7.5h18m-16.5 0V18m15-10.5V18M6 10.5h.008v.008H6V10.5zm12 5.25h.008v.008H18v-.008zM12 10.5a3 3 0 100 6 3 3 0 000-6z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Pembayaran</div>
                    <div class="text-xs text-slate-500">Verifikasi dan kelola</div>
                </div>
            </a>

            <a href="{{ route('admin.galeri.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-fuchsia-300 hover:bg-fuchsia-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-fuchsia-100 text-fuchsia-700 ring-1 ring-inset ring-fuchsia-200">
                    <!-- Heroicon: photo -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>

                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Galeri</div>
                    <div class="text-xs text-slate-500">Kelola foto & urutan</div>
                </div>
            </a>

            <a href="{{ route('admin.landing.settings') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-violet-300 hover:bg-violet-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-700 ring-1 ring-inset ring-violet-200">
                    <!-- Heroicon: paint-brush -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
</svg>

                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Landing Settings</div>
                    <div class="text-xs text-slate-500">Teks, kontak, dsb.</div>
                </div>
            </a>

            <a href="{{ route('admin.bank.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-teal-300 hover:bg-teal-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 text-teal-700 ring-1 ring-inset ring-teal-200">
                    <!-- Heroicon: building-library -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 19.5h18M4.5 10.5h15M6 19.5V9.75A3.75 3.75 0 019.75 6h4.5A3.75 3.75 0 0118 9.75V19.5"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Rekening Bank</div>
                    <div class="text-xs text-slate-500">Kelola bank penerima</div>
                </div>
            </a>

            <a href="{{ route('admin.users.index') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-rose-300 hover:bg-rose-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200">
                    <!-- Heroicon: users -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 18.75a6 6 0 10-12 0m12 0v.75m0-.75v-.75m-12 .75v.75m0-.75v-.75M15 7.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Pengguna</div>
                    <div class="text-xs text-slate-500">Kelola akun & role</div>
                </div>
            </a>

            {{-- <a href="{{ route('admin.laporan') }}" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 hover:bg-sky-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200">
                    <!-- Heroicon: chart-bar -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 19.5h18M6.75 17.25v-6m4.5 6V6.75m4.5 10.5v-3.75"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Laporan</div>
                    <div class="text-xs text-slate-500">Ringkasan & ekspor</div>
                </div>
            </a>
        </div> --}}
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Tren Booking (<span id="title-booking">{{ $monthOptions[$month] ?? $month }} {{ $year }}</span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartBookings"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Distribusi Status (<span id="title-status">{{ $monthOptions[$month] ?? $month }} {{ $year }}</span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
        <div class="lg:col-span-3 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Pendapatan Terkonfirmasi (<span id="title-revenue">{{ $monthOptions[$month] ?? $month }} {{ $year }}</span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        function setupDashboardCharts() {
            let labels = @json($labels);
            let bookings = @json($bookingsSeries);
            let revenue = @json($revenueSeries);
            let statusCounts = @json($statusCounts);

            // Fallbacks to keep charts visible when data is empty
            if (!Array.isArray(labels) || labels.length === 0) {
                labels = [''];
                bookings = [0];
                revenue = [0];
            }

            const sky = { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e' };
            const amber = { 400:'#fbbf24', 500:'#f59e0b', 600:'#d97706' };
            const rose = { 400:'#fb7185', 500:'#f43f5e', 600:'#e11d48' };
            const slate = { 400:'#94a3b8', 600:'#475569'};

            // Plugin to draw a center message when all dataset values are zero/empty
            const emptyMsgPlugin = {
                id: 'emptyMessage',
                afterDraw(chart, args, pluginOptions) {
                    const datasets = chart?.data?.datasets || [];
                    const hasData = datasets.some(ds => (ds?.data || []).some(v => Number(v || 0) > 0));
                    if (hasData) return;
                    const { ctx, chartArea } = chart;
                    if (!ctx || !chartArea) return;
                    ctx.save();
                    ctx.fillStyle = '#94a3b8'; // slate-400
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.font = '500 12px system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial';
                    const msg = pluginOptions?.message || 'Tidak ada data';
                    ctx.fillText(msg, (chartArea.left + chartArea.right)/2, (chartArea.top + chartArea.bottom)/2);
                    ctx.restore();
                }
            };
            if (window.Chart) { Chart.register(emptyMsgPlugin); }

            const ctxB = document.getElementById('chartBookings');
            const ctxS = document.getElementById('chartStatus');
            const ctxR = document.getElementById('chartRevenue');
            if (!window.Chart || !ctxB || !ctxS || !ctxR) return;

            window.__chartB = new Chart(ctxB, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{ label: 'Booking', data: bookings, borderColor: sky[600], backgroundColor: sky[200], tension: .3, fill: true, borderWidth: 2, pointRadius: 0 }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false }, emptyMessage: { message: 'Tidak ada data' } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
            });

            let statusLabels = Object.keys(statusCounts || {});
            let statusData = Object.values(statusCounts || {});
            if (statusLabels.length === 0) {
                statusLabels = ['Tidak ada data'];
                statusData = [0];
            }
            const statusColors = [sky[500], amber[500], rose[500], slate[400]];
            window.__chartS = new Chart(ctxS, {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: statusColors }] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' }, emptyMessage: { message: 'Tidak ada data' } } }
            });

            window.__chartR = new Chart(ctxR, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Pendapatan (Rp)', data: revenue, backgroundColor: sky[500], borderRadius: 6 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false }, emptyMessage: { message: 'Tidak ada data' } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
            });
        }

        function chartsReady() {
            const b = window.__chartB, s = window.__chartS, r = window.__chartR;
            // isConnected catches when Livewire re-renders and replaces canvases
            const ok = b && s && r && b.canvas?.isConnected && s.canvas?.isConnected && r.canvas?.isConnected;
            return !!ok;
        }

        function updateDashboardCharts(payload) {
            let { labels = [], bookings = [], revenue = [], status = {}, period = '' } = payload || {};
            if (!Array.isArray(labels) || labels.length === 0) {
                labels = [''];
                bookings = [0];
                revenue = [0];
            }
            // If charts not ready (destroyed by Livewire DOM patch), rebuild first
            if (!chartsReady()) {
                setupDashboardCharts();
            }
            // Update headings if period provided
            if (period) {
                const tb = document.getElementById('title-booking');
                const ts = document.getElementById('title-status');
                const tr = document.getElementById('title-revenue');
                if (tb) tb.textContent = period;
                if (ts) ts.textContent = period;
                if (tr) tr.textContent = period;
            }
            if (window.__chartB) {
                window.__chartB.data.labels = labels;
                window.__chartB.data.datasets[0].data = bookings;
                window.__chartB.update();
            }
            if (window.__chartR) {
                window.__chartR.data.labels = labels;
                window.__chartR.data.datasets[0].data = revenue;
                window.__chartR.update();
            }
            if (window.__chartS) {
                let sLabels = Object.keys(status || {});
                let sData = Object.values(status || {});
                if (sLabels.length === 0) { sLabels = ['Tidak ada data']; sData = [0]; }
                window.__chartS.data.labels = sLabels;
                window.__chartS.data.datasets[0].data = sData;
                window.__chartS.update();
            }
        }

        // Initial setup after page resources load
        window.addEventListener('load', () => {
            setupDashboardCharts();
        });

        // Listen to Livewire v3 browser event dispatched from PHP with $this->dispatch(...)
        window.addEventListener('dashboard-data-updated', (e) => {
            const d = e?.detail;
            const payload = Array.isArray(d) ? (d[0] || {}) : (d || {});
            updateDashboardCharts(payload);
        });

        // Ensure charts exist after any Livewire DOM morph/patch
        document.addEventListener('livewire:init', () => {
            if (!window.Livewire || !Livewire.hook) return;
            // v3 morph hooks
            if (Livewire.hook('morph.updated')) {
                Livewire.hook('morph.updated', () => { if (!chartsReady()) setupDashboardCharts(); });
                Livewire.hook('morph.added', () => { if (!chartsReady()) setupDashboardCharts(); });
            }
            // Back-compat fallback
            Livewire.hook('message.processed', () => { if (!chartsReady()) setupDashboardCharts(); });
        });

        // Safety net: watch the section for canvas replacements and re-init charts
        (function(){
            const target = document.currentScript?.closest('section') || document.body;
            if (!('MutationObserver' in window) || !target) return;
            const obs = new MutationObserver(() => { if (!chartsReady()) setupDashboardCharts(); });
            obs.observe(target, { childList: true, subtree: true });
        })();
    </script>
</section>
