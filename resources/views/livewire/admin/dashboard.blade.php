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
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-slate-600">Ringkasan singkat operasional Pondok Teges.</p>
        </div>
        <div class="flex items-center gap-2">
            <select wire:model.live.debounce.1000ms="month" class="ui-select">
                @foreach($monthOptions as $mVal => $mName)
                    <option value="{{ $mVal }}">{{ $mName }}</option>
                @endforeach
            </select>
            <select wire:model.live.debounce.1000ms="year" class="ui-select">
                @foreach($yearOptions as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <a href="{{ route('admin.laporan.export', ['from' => \Carbon\Carbon::create($year,$month,1)->startOfMonth()->format('Y-m-d'), 'to' => \Carbon\Carbon::create($year,$month,1)->endOfMonth()->format('Y-m-d')]) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">Export CSV</a>
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

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Tren Booking (30 hari)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartBookings"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Distribusi Status</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
        <div class="lg:col-span-3 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Pendapatan Terkonfirmasi (30 hari)</div>
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
            let { labels = [], bookings = [], revenue = [], status = {} } = payload || {};
            if (!Array.isArray(labels) || labels.length === 0) {
                labels = [''];
                bookings = [0];
                revenue = [0];
            }
            // If charts not ready (destroyed by Livewire DOM patch), rebuild first
            if (!chartsReady()) {
                setupDashboardCharts();
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

        window.addEventListener('load', () => {
            setupDashboardCharts();
        });

        document.addEventListener('livewire:load', () => {
            if (!window.Livewire) return;
            Livewire.on('dashboard-data-updated', (args) => {
                const payload = Array.isArray(args) ? (args[0] || {}) : (args || {});
                updateDashboardCharts(payload);
            });
            // When Livewire finishes any DOM patch, ensure charts exist
            if (Livewire.hook) {
                Livewire.hook('message.processed', () => {
                    if (!chartsReady()) {
                        setupDashboardCharts();
                    }
                });
            }
        });
    </script>
</section>
