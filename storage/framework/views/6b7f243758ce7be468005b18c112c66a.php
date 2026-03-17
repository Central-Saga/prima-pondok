<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Livewire\Volt\Component;

?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Dashboard</h1>
            <p class="mt-1 text-slate-600">Ringkasan singkat operasional Pondok Teges.</p>
        </div>
        <div class="flex items-center gap-2" wire:poll.3s="refreshNotifications">
            <select wire:model.live.debounce.1000ms="month" class="ui-select w-auto min-w-[150px]">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $monthOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mVal => $mName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($mVal); ?>"><?php echo e($mName); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <select wire:model.live.debounce.1000ms="year" class="ui-select">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $yearOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <a href="<?php echo e(route('admin.laporan.export', ['from' => \Carbon\Carbon::create($year,$month,1)->startOfMonth()->format('Y-m-d'), 'to' => \Carbon\Carbon::create($year,$month,1)->endOfMonth()->format('Y-m-d')])); ?>" class="ui-btn-secondary whitespace-nowrap">Export Excel</a>

            <details class="relative">
                <summary class="list-none cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2 text-slate-700 shadow-sm hover:bg-slate-50">
                    <span class="relative inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9A6 6 0 0 0 6 9v.05-.05v.7a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingBookingCount > 0): ?>
                            <span class="absolute -right-2 -top-2 inline-flex min-w-5 items-center justify-center rounded-full bg-rose-600 px-1.5 py-0.5 text-[11px] font-semibold leading-none text-white">
                                <?php echo e($pendingBookingCount > 99 ? '99+' : $pendingBookingCount); ?>

                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </summary>

                <div class="absolute right-0 z-20 mt-2 w-[360px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">Notifikasi Pemesanan</div>
                        <a href="<?php echo e(route('admin.pemesanan.index')); ?>" class="text-xs font-medium text-sky-700 hover:text-sky-800">Lihat semua</a>
                    </div>

                    <div class="max-h-80 overflow-auto">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($pendingBookingItems)): ?>
                            <div class="px-4 py-4 text-sm text-slate-600">Tidak ada pemesanan pending.</div>
                        <?php else: ?>
                            <ul class="divide-y divide-slate-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pendingBookingItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a href="<?php echo e(route('admin.pemesanan.index', ['highlight' => $n['id']])); ?>" class="block px-4 py-3 hover:bg-slate-50">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <div class="text-sm font-medium text-slate-900 truncate">#<?php echo e($n['id']); ?> · <?php echo e($n['wisatawan']); ?></div>
                                                    <div class="mt-0.5 text-xs text-slate-600 truncate"><?php echo e($n['kamar']); ?></div>
                                                    <div class="mt-1 text-xs text-slate-500"><?php echo e($n['checkin']); ?> → <?php echo e($n['checkout']); ?></div>
                                                </div>
                                                <div class="text-[11px] text-slate-500 whitespace-nowrap"><?php echo e($n['created_human']); ?></div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </details>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="rounded-2xl bg-gradient-to-r from-sky-500 to-sky-400 text-white p-6 shadow-sm">
            <div class="text-sm/6 opacity-90">Total Kamar</div>
            <div class="mt-2 text-4xl font-semibold"><?php echo e($kamarCount); ?></div>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">
            <div class="text-sm/6 text-slate-600">Total Pemesanan</div>
            <div class="mt-2 text-4xl font-semibold text-slate-900"><?php echo e($pemesananCount); ?></div>
        </div>
        <div class="rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-400 text-white p-6 shadow-sm">
            <div class="text-sm/6 opacity-90">Total Pendapatan</div>
            <div class="mt-2 text-3xl sm:text-4xl font-semibold">Rp <?php echo e(number_format($totalRevenue,0,',','.')); ?></div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="text-slate-900 font-medium mb-3">Aksi Cepat</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="<?php echo e(route('admin.kamar.create')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-sky-300 hover:bg-sky-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-100 text-sky-700 ring-1 ring-inset ring-sky-200">
                    <!-- Heroicon: plus-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Tambah Kamar</div>
                    <div class="text-xs text-slate-500">Buat entri kamar baru</div>
                </div>
            </a>

            <a href="<?php echo e(route('admin.pemesanan.create')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-emerald-300 hover:bg-emerald-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    <!-- Heroicon: calendar-days -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3 8.25h18M4.5 7.5v11.25A2.25 2.25 0 006.75 21h10.5A2.25 2.25 0 0019.5 18.75V7.5M8.25 12h.008v.008H8.25V12zm3 0h.008v.008H11.25V12zm3 0h.008v.008H14.25V12z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Buat Pemesanan</div>
                    <div class="text-xs text-slate-500">Input booking manual</div>
                </div>
            </a>

            <a href="<?php echo e(route('admin.pemesanan.index')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-slate-300 hover:bg-slate-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200">
                    <!-- Heroicon: list-bullet -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.008v.008H3.75V6.75zm0 5.25h.008v.008H3.75V12zm0 5.25h.008v.008H3.75v-.008z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Daftar Booking</div>
                    <div class="text-xs text-slate-500">Pantau semua pemesanan</div>
                </div>
            </a>

            <a href="<?php echo e(route('admin.galeri.index')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-fuchsia-300 hover:bg-fuchsia-50 transition flex items-center gap-3">
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

            <a href="<?php echo e(route('admin.fasilitas.index')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-violet-300 hover:bg-violet-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-700 ring-1 ring-inset ring-violet-200">
                    <!-- Heroicon: wrench-screwdriver -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />
</svg>


                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Fasilitas</div>
                    <div class="text-xs text-slate-500">Kelola fasilitas kamar</div>
                </div>
            </a>

            <a href="<?php echo e(route('admin.bank.index')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-teal-300 hover:bg-teal-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-teal-100 text-teal-700 ring-1 ring-inset ring-teal-200">
                    <!-- Heroicon: building-library -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 19.5h18M4.5 10.5h15M6 19.5V9.75A3.75 3.75 0 019.75 6h4.5A3.75 3.75 0 0118 9.75V19.5"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Rekening Bank</div>
                    <div class="text-xs text-slate-500">Kelola bank penerima</div>
                </div>
            </a>

            <a href="<?php echo e(route('admin.users.index')); ?>" class="group rounded-xl border border-slate-200 bg-white p-4 hover:border-rose-300 hover:bg-rose-50 transition flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200">
                    <!-- Heroicon: users -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18 18.75a6 6 0 10-12 0m12 0v.75m0-.75v-.75m-12 .75v.75m0-.75v-.75M15 7.5a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <div>
                    <div class="text-sm font-medium text-slate-900">Pengguna</div>
                    <div class="text-xs text-slate-500">Kelola akun & role</div>
                </div>
            </a>

            
    </div>

    <div wire:ignore id="dashboard-charts" class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Tren Booking (<span id="title-booking"><?php echo e($monthOptions[$month] ?? $month); ?> <?php echo e($year); ?></span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartBookings"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Distribusi Status (<span id="title-status"><?php echo e($monthOptions[$month] ?? $month); ?> <?php echo e($year); ?></span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
        <div class="lg:col-span-3 rounded-2xl border border-sky-100 bg-white p-4 shadow-sm">
            <div class="text-slate-900 font-medium">Pendapatan Terkonfirmasi (<span id="title-revenue"><?php echo e($monthOptions[$month] ?? $month); ?> <?php echo e($year); ?></span>)</div>
            <div class="mt-3" style="height:220px">
                <canvas id="chartRevenue"></canvas>
            </div>
        </div>
    </div>

    <?php if (! $__env->hasRenderedOnce('8cf0bec9-907f-468a-9250-677e2cdc337c')): $__env->markAsRenderedOnce('8cf0bec9-907f-468a-9250-677e2cdc337c'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        function setupDashboardCharts() {
            let labels = <?php echo json_encode($labels, 15, 512) ?>;
            let bookings = <?php echo json_encode($bookingsSeries, 15, 512) ?>;
            let revenue = <?php echo json_encode($revenueSeries, 15, 512) ?>;
            let statusCounts = <?php echo json_encode($statusCounts, 15, 512) ?>;

            // Fallbacks to keep charts visible when data is empty
            if (!Array.isArray(labels) || labels.length === 0) {
                labels = [''];
                bookings = [0];
                revenue = [0];
            }

            const sky = { 50:'#f0f9ff',100:'#e0f2fe',200:'#bae6fd',300:'#7dd3fc',400:'#38bdf8',500:'#0ea5e9',600:'#0284c7',700:'#0369a1',800:'#075985',900:'#0c4a6e' };
            const amber = { 400:'#fbbf24', 500:'#f59e0b', 600:'#d97706' };
            const rose = { 400:'#fb7185', 500:'#f43f5e', 600:'#e11d48' };
            const emerald = { 400:'#34d399', 500:'#10b981', 600:'#059669' };
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
            // Pastikan kanvas sudah terpasang dan punya ukuran > 0
            const readyCanvases = [ctxB, ctxS, ctxR].every(el => el && el.isConnected && (el.offsetWidth > 0 || el.width > 0));
            if (!window.Chart || !readyCanvases) return;

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
            // Samakan warna chart dengan badge status pemesanan
            const statusColorOf = (label) => {
                const s = String(label || '').toLowerCase();
                if (s === 'pending') return amber[500];
                if (s === 'confirmed') return emerald[500];
                if (s === 'cancelled' || s === 'canceled') return rose[500];
                if (s === 'completed') return sky[500];
                // fallback yang masih konsisten dengan komponen badge
                if (s === 'verified' || s === 'available' || s === 'active' || s === 'wisatawan') return emerald[500];
                if (s === 'rejected' || s === 'unavailable') return rose[500];
                if (s === 'maintenance') return amber[500];
                if (s === 'admin') return sky[500];
                return slate[400];
            };
            const statusColors = statusLabels.map(l => statusColorOf(l));
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

        // Coba inisialisasi berulang sampai Chart.js & kanvas siap (mengatasi kasus pertama kali login)
        let __chartRetries = 0;
        function trySetupCharts() {
            if (chartsReady()) return;
            const b = document.getElementById('chartBookings');
            const s = document.getElementById('chartStatus');
            const r = document.getElementById('chartRevenue');
            const haveCanvas = [b,s,r].every(el => el && el.isConnected && (el.offsetWidth > 0 || el.width > 0));
            if (window.Chart && haveCanvas) {
                setupDashboardCharts();
                return;
            }
            if (__chartRetries < 25) { // ~3-4 detik total
                __chartRetries++;
                setTimeout(trySetupCharts, 150);
            }
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
                const statusColorOf = (label) => {
                    const s = String(label || '').toLowerCase();
                    if (s === 'pending') return '#f59e0b'; // amber-500
                    if (s === 'confirmed') return '#10b981'; // emerald-500
                    if (s === 'cancelled' || s === 'canceled') return '#f43f5e'; // rose-500
                    if (s === 'completed') return '#0ea5e9'; // sky-500
                    if (s === 'verified' || s === 'available' || s === 'active' || s === 'wisatawan') return '#10b981';
                    if (s === 'rejected' || s === 'unavailable') return '#f43f5e';
                    if (s === 'maintenance') return '#f59e0b';
                    if (s === 'admin') return '#0ea5e9';
                    return '#94a3b8'; // slate-400
                };
                window.__chartS.data.labels = sLabels;
                window.__chartS.data.datasets[0].data = sData;
                window.__chartS.data.datasets[0].backgroundColor = sLabels.map(l => statusColorOf(l));
                window.__chartS.update();
            }
        }

        // Initial setup after page resources load
        window.addEventListener('load', () => { trySetupCharts(); });

        // Untuk navigasi Livewire (wire:navigate)
        window.addEventListener('livewire:navigated', () => { __chartRetries = 0; trySetupCharts(); });

        // Listen to Livewire v3 browser event dispatched from PHP with $this->dispatch(...)
        window.addEventListener('dashboard-data-updated', (e) => {
            const d = e?.detail;
            const payload = Array.isArray(d) ? (d[0] || {}) : (d || {});
            // pastikan grafik terbuat dulu
            if (!chartsReady()) { __chartRetries = 0; trySetupCharts(); setTimeout(() => updateDashboardCharts(payload), 200); }
            else { updateDashboardCharts(payload); }
        });

        // Ensure charts exist after any Livewire DOM morph/patch
        document.addEventListener('livewire:init', () => {
            if (!window.Livewire || !Livewire.hook) return;
            // v3 morph hooks
            if (Livewire.hook('morph.updated')) {
                Livewire.hook('morph.updated', () => { if (!chartsReady()) trySetupCharts(); });
                Livewire.hook('morph.added', () => { if (!chartsReady()) trySetupCharts(); });
            }
            // Back-compat fallback
            Livewire.hook('message.processed', () => { if (!chartsReady()) trySetupCharts(); });
        });

        // Safety net: watch the section for canvas replacements and re-init charts
        (function(){
            const target = document.currentScript?.closest('section') || document.body;
            if (!('MutationObserver' in window) || !target) return;
            const obs = new MutationObserver(() => { if (!chartsReady()) trySetupCharts(); });
            obs.observe(target, { childList: true, subtree: true });
        })();

        // ResizeObserver untuk memastikan grafik merender ketika ukuran kontainer berubah dari 0
        (function(){
            if (!('ResizeObserver' in window)) return;
            const el = document.getElementById('chartBookings')?.parentElement?.parentElement; // card besar
            const ro = new ResizeObserver(() => {
                if (chartsReady()) { window.__chartB.resize(); window.__chartR.resize(); }
                else { trySetupCharts(); }
            });
            if (el) ro.observe(el);
        })();
    </script>
    <?php endif; ?>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/admin/dashboard.blade.php ENDPATH**/ ?>