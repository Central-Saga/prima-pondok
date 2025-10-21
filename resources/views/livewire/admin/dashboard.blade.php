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

    // Chart data (last 30 days)
    public array $labels = [];
    public array $bookingsSeries = [];
    public array $revenueSeries = [];
    public array $statusCounts = [];

    public function mount(): void
    {
        $this->kamarCount = Kamar::count();
        $this->pemesananCount = Pemesanan::count();
        $this->pembayaranPending = Pembayaran::where('status', 'pending')->count();
        $this->totalRevenue = (float) Pembayaran::where('status','verified')->sum('jumlah');

        $from = now()->subDays(29)->startOfDay();
        $days = collect();
        for ($i = 0; $i < 30; $i++) {
            $day = $from->copy()->addDays($i);
            $days->push($day);
        }
        $this->labels = $days->map(fn($d) => $d->format('d M'))->toArray();

        $bookingRaw = Pemesanan::where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $revenueRaw = Pembayaran::where('status', 'verified')
            ->where('created_at', '>=', $from)
            ->selectRaw('DATE(created_at) as d, SUM(jumlah) as s')
            ->groupBy('d')
            ->pluck('s', 'd');

        $this->bookingsSeries = $days->map(fn($d) => (int) ($bookingRaw[$d->format('Y-m-d')] ?? 0))->toArray();
        $this->revenueSeries = $days->map(fn($d) => (float) ($revenueRaw[$d->format('Y-m-d')] ?? 0))->toArray();

        $this->statusCounts = Pemesanan::where('created_at','>=',$from)
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
        window.addEventListener('load', () => {
            const labels = @json($labels);
            const bookings = @json($bookingsSeries);
            const revenue = @json($revenueSeries);
            const statusCounts = @json($statusCounts);

            const sky = { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e' };
            const amber = { 400:'#fbbf24', 500:'#f59e0b', 600:'#d97706' };
            const rose = { 400:'#fb7185', 500:'#f43f5e', 600:'#e11d48' };
            const slate = { 400:'#94a3b8', 600:'#475569'};

            const ctxB = document.getElementById('chartBookings');
            const ctxS = document.getElementById('chartStatus');
            const ctxR = document.getElementById('chartRevenue');
            if (!window.Chart || !ctxB || !ctxS || !ctxR) return;

            new Chart(ctxB, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{ label: 'Booking', data: bookings, borderColor: sky[600], backgroundColor: sky[200], tension: .3, fill: true, borderWidth: 2, pointRadius: 0 }]
                },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
            });

            const statusLabels = Object.keys(statusCounts);
            const statusData = Object.values(statusCounts);
            const statusColors = [sky[500], amber[500], rose[500], slate[400]];
            new Chart(ctxS, {
                type: 'doughnut',
                data: { labels: statusLabels, datasets: [{ data: statusData, backgroundColor: statusColors }] },
                options: { maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(ctxR, {
                type: 'bar',
                data: { labels, datasets: [{ label: 'Pendapatan (Rp)', data: revenue, backgroundColor: sky[500], borderRadius: 6 }] },
                options: { maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
            });
        });
    </script>
</section>
