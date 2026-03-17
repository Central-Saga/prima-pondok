<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Kamar;
use App\Models\Galeri;
use App\Models\Setting;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    $kamar = Kamar::query()->whereIn('status', ['available', 'maintenance'])->latest()->take(6)->get();
    $hasMoreKamar = Kamar::query()->whereIn('status', ['available', 'maintenance'])->count() > 6;
    $galeri = Galeri::query()->where('status', 'active')->orderBy('urutan')->take(8)->get();
    $reviews = Review::query()
        ->with(['kamar','wisatawan'])
        ->where('is_published', true)
        ->latest()
        ->take(6)
        ->get();
    $hero_title = Setting::get('hero_title', 'Home Stay Pondok Teges');
    $hero_subtitle = Setting::get('hero_subtitle', 'Rasakan kenyamanan menginap di Ubud.');
    $contact_phone = Setting::get('contact_phone', '+62-812-0000-0000');
    $contact_email = Setting::get('contact_email', 'info@pondokteges.local');
    $contact_address = Setting::get('contact_address', 'Ubud, Bali - Indonesia');
    return view('landing', compact('kamar', 'galeri', 'reviews', 'hero_title', 'hero_subtitle', 'contact_phone', 'contact_email', 'contact_address', 'hasMoreKamar'));
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
// Admin - Bank CRUD pages
Volt::route('admin/bank', 'admin.bank.index')->middleware(['auth', 'role:admin'])->name('admin.bank.index');
Volt::route('admin/bank/create', 'admin.bank.create')->middleware(['auth', 'role:admin'])->name('admin.bank.create');
Volt::route('admin/bank/{bank}/edit', 'admin.bank.edit')->middleware(['auth', 'role:admin'])->name('admin.bank.edit');
// Admin - Fasilitas CRUD pages
Volt::route('admin/fasilitas', 'admin.fasilitas.index')->middleware(['auth', 'role:admin'])->name('admin.fasilitas.index');
Volt::route('admin/fasilitas/create', 'admin.fasilitas.create')->middleware(['auth', 'role:admin'])->name('admin.fasilitas.create');
Volt::route('admin/fasilitas/{fasilitas}/edit', 'admin.fasilitas.edit')->middleware(['auth', 'role:admin'])->name('admin.fasilitas.edit');
// Admin - Pemesanan pages
Volt::route('admin/pemesanan', 'admin.pemesanan.index')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.index');
Volt::route('admin/pemesanan/create', 'admin.pemesanan.create')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.create');
Volt::route('admin/pemesanan/{pemesanan}/edit', 'admin.pemesanan.edit')->middleware(['auth', 'role:admin'])->name('admin.pemesanan.edit');
// Admin - Laporan
Volt::route('admin/laporan', 'admin.laporan.index')->middleware(['auth', 'role:admin'])->name('admin.laporan');
Volt::route('admin/landing-settings', 'admin.landing-settings')->middleware(['auth', 'role:admin'])->name('admin.landing.settings');
// Admin - Review pages
Volt::route('admin/review', 'admin.review.index')->middleware(['auth', 'role:admin'])->name('admin.review.index');
Volt::route('admin/review/create', 'admin.review.create')->middleware(['auth', 'role:admin'])->name('admin.review.create');
Volt::route('admin/review/{review}/edit', 'admin.review.edit')->middleware(['auth', 'role:admin'])->name('admin.review.edit');
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

    $bookings = \App\Models\Pemesanan::with(['wisatawan','kamar'])
        ->whereBetween('created_at', [$qFrom, $qTo])
        ->orderBy('id')
        ->get();

    // If the provided range exactly matches one calendar month, name file per month
    $isFullMonth = $from->copy()->startOfDay()->equalTo($from->copy()->startOfMonth()->startOfDay())
        && $to->copy()->endOfDay()->equalTo($to->copy()->endOfMonth()->endOfDay())
        && $from->isSameMonth($to);

    $months = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    
    if ($isFullMonth) {
        $filename = sprintf('Laporan Bulan %s %d.xlsx', $months[(int) $from->month] ?? $from->format('F'), (int) $from->year);
    } else {
        $filename = sprintf('Laporan Tanggal %s sampai %s.xlsx',
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );
    }

    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\PemesananExport($bookings, (int) $from->month, (int) $from->year),
        $filename
    );
})->name('admin.laporan.export');
Volt::route('booking/{pemesanan}', 'wisatawan.booking-show')->middleware(['auth', 'role:wisatawan'])->name('booking.show');
Volt::route('booking', 'wisatawan.booking-index')->middleware(['auth', 'role:wisatawan'])->name('booking.index');
Volt::route('booking/{pemesanan}/extend', 'wisatawan.booking-extend')->middleware(['auth', 'role:wisatawan'])->name('booking.extend');
Volt::route('booking/{pemesanan}/review', 'wisatawan.review-create')->middleware(['auth', 'role:wisatawan'])->name('booking.review');
Volt::route('booking/{pemesanan}/print', 'wisatawan.booking-print')->middleware(['auth', 'role:wisatawan'])->name('booking.print');
Volt::route('akun/profil', 'wisatawan.profile')->middleware(['auth', 'role:wisatawan'])->name('wisatawan.profile');
Volt::route('akun/password', 'wisatawan.password')->middleware(['auth', 'role:wisatawan'])->name('wisatawan.password');
// Public kamar listing and detail
Volt::route('kamar', 'public.kamar-index')->name('kamar.index');
Volt::route('kamar/{kamar}', 'public.kamar-show')->name('kamar.show');
// About page
Volt::route('about', 'public.about')->name('about');

Route::get('lang/{locale}', function (string $locale) {
    if (! in_array($locale, ['id', 'en'], true)) {
        abort(404);
    }

    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

require __DIR__.'/auth.php';

// Guard against accidental GET requests to Livewire update endpoint (some browsers/prefetchers)
Route::get('/livewire/update', function () {
    return response()->noContent();
});
