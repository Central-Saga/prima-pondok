<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Stay Pondok Teges</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="antialiased bg-white text-slate-800">
    <!-- Navbar -->
    @include('partials.site-header')

    <!-- Hero -->
    <section class="relative isolate overflow-hidden bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
            <div class="max-w-2xl">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">{{ $hero_title ?? 'Home Stay Pondok Teges' }}</h1>
                <p class="mt-6 text-lg leading-8 text-slate-600">{{ $hero_subtitle ?? 'Rasakan kenyamanan menginap di kawasan Ubud.' }}</p>
                <div class="mt-10 flex items-center gap-x-6">
                    <a href="#kamar" class="rounded-md bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-slate-700">Lihat Kamar</a>
                    <a href="#kontak" class="text-sm font-semibold leading-6 text-slate-900">Kontak Kami →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Kamar -->
    <section id="kamar" class="py-16 sm:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Kamar Tersedia</h2>
            </div>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse(($kamar ?? []) as $item)
                    <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
                        @if(($item->fotos ?? collect())->isNotEmpty())
                            <div class="h-40 bg-slate-100">
                                <img src="{{ url('media/'.$item->fotos->first()->path) }}" alt="{{ $item->nama_kamar }}" class="h-full w-full object-cover">
                            </div>
                        @else
                            <div class="h-40 bg-slate-100 flex items-center justify-center text-slate-400">Foto</div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-slate-900">{{ $item->nama_kamar }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ $item->tipe_kamar ?: 'Tipe Standar' }}</p>
                            <p class="mt-2 font-semibold text-slate-900">Rp {{ number_format($item->harga, 0, ',', '.') }}/malam</p>
                            <div class="mt-4">
                                <a href="{{ route('kamar.show', $item->id) }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">Pesan Sekarang</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-slate-600">Belum ada data kamar.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Fasilitas simple highlight -->
    <section id="fasilitas" class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Fasilitas</h2>
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm text-slate-700">
                <div class="rounded-lg border bg-white px-4 py-3">Wi‑Fi</div>
                <div class="rounded-lg border bg-white px-4 py-3">Parkir</div>
                <div class="rounded-lg border bg-white px-4 py-3">AC / Kipas</div>
                <div class="rounded-lg border bg-white px-4 py-3">Air Panas</div>
                <div class="rounded-lg border bg-white px-4 py-3">Sarapan (opsional)</div>
                <div class="rounded-lg border bg-white px-4 py-3">Layanan Kebersihan</div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section id="galeri" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Galeri</h2>
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse(($galeri ?? []) as $g)
                    <div class="aspect-[4/3] overflow-hidden rounded-lg border bg-slate-100">
                        @if(\Illuminate\Support\Str::startsWith($g->path, ['http://','https://']))
                            <img src="{{ $g->path }}" alt="{{ $g->title }}" class="h-full w-full object-cover">
                        @else
                            <img src="{{ url('media/'.$g->path) }}" alt="{{ $g->title }}" class="h-full w-full object-cover">
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-slate-600">Belum ada foto.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Kontak / Lokasi -->
    <section id="kontak" class="py-16 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 sm:grid-cols-2">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold">Kontak</h2>
                <p class="mt-4 text-slate-200">{{ $contact_address ?? 'Ubud, Bali — Indonesia' }}</p>
                <p class="mt-2 text-slate-200">Telepon/WA: {{ $contact_phone ?? '' }}</p>
                <p class="mt-2 text-slate-200">Email: {{ $contact_email ?? '' }}</p>
                <div class="mt-6">
                    <a href="https://wa.me/{{ preg_replace('/\D/','',$contact_phone ?? '') }}" class="inline-flex items-center rounded-md bg-white/10 px-4 py-2 text-sm font-medium hover:bg-white/20">Chat WhatsApp</a>
                </div>
            </div>
            <div class="rounded-xl overflow-hidden border border-white/10 bg-white/5 min-h-56 flex items-center justify-center">
                <span class="text-white/70 text-sm">Peta lokasi</span>
            </div>
        </div>
    </section>

    @include('partials.site-footer')
</body>
</html>

