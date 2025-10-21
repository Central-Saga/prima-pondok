<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Kamar;
use App\Models\Galeri;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $kamar = Kamar::query()->with('fotos')->where('status', 'available')->latest()->take(6)->get();
    $galeri = Galeri::query()->where('status', 'active')->orderBy('urutan')->take(8)->get();
    $hero_title = Setting::get('hero_title', 'Home Stay Pondok Teges');
    $hero_subtitle = Setting::get('hero_subtitle', 'Rasakan kenyamanan menginap di Ubud.');
    $contact_phone = Setting::get('contact_phone', '+62-812-0000-0000');
    $contact_email = Setting::get('contact_email', 'info@pondokteges.local');
    $contact_address = Setting::get('contact_address', 'Ubud, Bali — Indonesia');
    return view('landing', compact('kamar', 'galeri', 'hero_title', 'hero_subtitle', 'contact_phone', 'contact_email', 'contact_address'));
})->name('home');

// Public media route (works even if storage symlink not available on OS)
Route::get('media/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);
    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('media');

Route::get('dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('home');
    }
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('wisatawan')) {
        return redirect()->route('home');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Dashboards
Volt::route('admin', 'admin.dashboard')->middleware(['auth', 'role:admin'])->name('admin.dashboard');
// Admin - Kamar CRUD pages
Volt::route('admin/kamar', 'admin.kamar.index')->middleware(['auth', 'role:admin'])->name('admin.kamar.index');
Volt::route('admin/kamar/create', 'admin.kamar.create')->middleware(['auth', 'role:admin'])->name('admin.kamar.create');
Volt::route('admin/kamar/{kamar}/edit', 'admin.kamar.edit')->middleware(['auth', 'role:admin'])->name('admin.kamar.edit');

// Admin - Galeri CRUD pages
Volt::route('admin/galeri', 'admin.galeri.index')->middleware(['auth', 'role:admin'])->name('admin.galeri.index');
Volt::route('admin/galeri/create', 'admin.galeri.create')->middleware(['auth', 'role:admin'])->name('admin.galeri.create');
Volt::route('admin/galeri/{galeri}/edit', 'admin.galeri.edit')->middleware(['auth', 'role:admin'])->name('admin.galeri.edit');
Volt::route('admin/galeri/order', 'admin.galeri.order')->middleware(['auth', 'role:admin'])->name('admin.galeri.order');
Volt::route('admin/pembayaran', 'admin.pembayaran-index')->middleware(['auth', 'role:admin'])->name('admin.pembayaran');
Volt::route('admin/pembayaran/create', 'admin.pembayaran.create')->middleware(['auth', 'role:admin'])->name('admin.pembayaran.create');
Volt::route('admin/pembayaran/{pembayaran}/edit', 'admin.pembayaran.edit')->middleware(['auth', 'role:admin'])->name('admin.pembayaran.edit');
// Admin - Bank CRUD pages
Volt::route('admin/bank', 'admin.bank.index')->middleware(['auth', 'role:admin'])->name('admin.bank.index');
Volt::route('admin/bank/create', 'admin.bank.create')->middleware(['auth', 'role:admin'])->name('admin.bank.create');
Volt::route('admin/bank/{bank}/edit', 'admin.bank.edit')->middleware(['auth', 'role:admin'])->name('admin.bank.edit');
// Admin - Pemesanan pages
Volt::route('admin/pemesanan', 'admin.pemesanan.index')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.index');
Volt::route('admin/pemesanan/create', 'admin.pemesanan.create')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.create');
Volt::route('admin/pemesanan/{pemesanan}/edit', 'admin.pemesanan.edit')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.edit');
Volt::route('admin/pemesanan/{pemesanan}', 'admin.pemesanan.show')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.show');
// Admin - Laporan
Volt::route('admin/laporan', 'admin.laporan.index')->middleware(['auth', 'role:admin'])->name('admin.laporan');
Volt::route('admin/landing-settings', 'admin.landing-settings')->middleware(['auth', 'role:admin'])->name('admin.landing.settings');
// Admin - Users
Volt::route('admin/users', 'admin.users.index')->middleware(['auth', 'role:admin'])->name('admin.users.index');
Volt::route('admin/users/create', 'admin.users.create')->middleware(['auth', 'role:admin'])->name('admin.users.create');
Volt::route('admin/users/{user}/edit', 'admin.users.edit')->middleware(['auth', 'role:admin'])->name('admin.users.edit');

Route::get('admin/laporan/export', function (\Illuminate\Http\Request $request) {
    abort_unless(auth()->check() && auth()->user()->hasRole('admin'), 403);
    $from = $request->filled('from')
        ? \Illuminate\Support\Carbon::parse($request->input('from'))
        : now()->startOfMonth();
    $to = $request->filled('to')
        ? \Illuminate\Support\Carbon::parse($request->input('to'))
        : now();

    // Clone for querying so original $from/$to still available for filename logic
    $qFrom = $from->copy()->startOfDay();
    $qTo = $to->copy()->endOfDay();

    $rows = \App\Models\Pemesanan::with(['wisatawan','kamar'])
        ->whereBetween('created_at', [$qFrom, $qTo])
        ->orderBy('id')
        ->get();

    // If the provided range exactly matches one calendar month, name file per month
    $isFullMonth = $from->copy()->startOfDay()->equalTo($from->copy()->startOfMonth()->startOfDay())
        && $to->copy()->endOfDay()->equalTo($to->copy()->endOfMonth()->endOfDay())
        && $from->isSameMonth($to);

    if ($isFullMonth) {
        $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $filename = sprintf('Laporan Bulan %s %d.csv', $months[(int) $from->month] ?? $from->format('F'), (int) $from->year);
    } else {
        $filename = sprintf('Laporan Tanggal %s sampai %s.csv',
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );
    }
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($rows) {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Wisatawan','Kamar','Checkin','Checkout','Malam','Total','Status']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r->id,
                optional($r->wisatawan)->name,
                optional($r->kamar)->nama_kamar,
                optional($r->tanggal_checkin)?->format('Y-m-d'),
                optional($r->tanggal_checkout)?->format('Y-m-d'),
                $r->jumlah_hari,
                $r->total_bayar,
                $r->status,
            ]);
        }
        fclose($out);
    };

    return response()->stream($callback, 200, $headers);
})->name('admin.laporan.export');
Volt::route('booking/{pemesanan}', 'wisatawan.booking-show')->middleware(['auth', 'role:wisatawan'])->name('booking.show');
Volt::route('booking', 'wisatawan.booking-index')->middleware(['auth', 'role:wisatawan'])->name('booking.index');
Volt::route('kamar/{kamar}', 'public.kamar-show')->name('kamar.show');

require __DIR__.'/auth.php';

// Guard against accidental GET requests to Livewire update endpoint (some browsers/prefetchers)
Route::get('/livewire/update', function () {
    return response()->noContent();
});
