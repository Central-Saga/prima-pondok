<x-layouts.public>
    @php
        $heroTitle = \App\Models\Setting::get('hero_title', 'Home Stay Pondok Teges');
        $heroSubtitle = \App\Models\Setting::get('hero_subtitle', 'Rasakan kenyamanan menginap di kawasan Ubud.');
    @endphp

    <!-- Hero -->
    <section class="relative isolate overflow-hidden bg-gradient-to-b from-sky-50 to-white">
        @php($heroImages = collect($galeri ?? [])->take(6))
        <div id="heroCarousel" class="absolute inset-0">
            @forelse($heroImages as $idx => $g)
                @php($p = trim((string)($g->path ?? '')))
                @continue($p === '')
                @php($src = \Illuminate\Support\Str::startsWith($p, ['http://','https://']) ? $p : asset('storage/'.$p))
                <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" data-slide>
                    <img src="{{ $src }}" alt="{{ $g->title ?? 'Galeri' }}" class="h-full w-full object-cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('storage/login/login reg page.webp') }}'">
                </div>
            @empty
                <div class="absolute inset-0 bg-slate-100"></div>
            @endforelse
            <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/20 to-transparent pointer-events-none"></div>
            <div class="absolute inset-0">
                <div class="mx-auto max-w-7xl h-full px-4 sm:px-6 lg:px-8 relative">
                
                    @if($heroImages->count() > 1)
                    <div class="absolute bottom-6 inset-x-0 flex items-center justify-center gap-2 z-20">
                        @foreach($heroImages as $i => $g)
                            @php($p = trim((string)($g->path ?? '')))
                            @continue($p === '')
                            <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/70 ring-1 ring-black/10 data-[active=true]:bg-white data-[active=true]:w-6 transition-[width,background-color] duration-300" data-idx="{{ $i }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 sm:py-28 relative">
            <div class="grid gap-10 lg:grid-cols-2 items-center">
                <div class="max-w-2xl relative z-10">
                <h1 class="text-4xl font-bold tracking-tight text-white drop-shadow-md sm:text-5xl">{{ $heroTitle }}</h1>
                <p class="mt-6 text-lg leading-8 text-white/90">{{ $heroSubtitle }}</p>
                <div class="mt-10 flex items-center gap-x-3 sm:gap-x-4">
                    <a href="#kamar" class="ui-btn-primary !bg-sky-600 hover:!bg-sky-500 ring-1 ring-white/20">Lihat Kamar</a>
                    <a href="#kontak" class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-medium text-white backdrop-blur-md shadow-sm transition-colors hover:bg-white/20 hover:border-white/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60">
                        Kontak Kami
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69l-3.47-3.47a.75.75 0 1 1 1.06-1.06l4.75 4.75a.75.75 0 0 1 0 1.06l-4.75 4.75a.75.75 0 0 1-1.06-1.06l3.47-3.47H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/></svg>
                    </a>
                </div>
                @php($heroImages = collect($galeri ?? [])->take(6))
                <div class="relative hidden" aria-hidden="true">
                    <div id="heroCarouselHidden" class="absolute inset-0">
                        @forelse($heroImages as $idx => $g)
                            @php($p = trim((string)($g->path ?? '')))
                            @continue($p === '')
                            @php($src = \Illuminate\Support\Str::startsWith($p, ['http://','https://']) ? $p : asset('storage/'.$p))
                            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" data-slide>
                                <img src="{{ $src }}" alt="{{ $g->title ?? 'Galeri' }}" class="h-full w-full object-cover" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('storage/login/login reg page.webp') }}'">
                            </div>
                        @empty
                            <div class="absolute inset-0 grid place-items-center text-slate-400">Tidak ada gambar</div>
                        @endforelse
                        <div class="absolute inset-0 bg-gradient-to-b from-black/45 via-black/20 to-transparent pointer-events-none"></div>


                        @if($heroImages->count() > 1)
                        <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-2 z-20">
                            @foreach($heroImages as $i => $g)
                                @php($p = trim((string)($g->path ?? '')))
                                @continue($p === '')
                                <button type="button" class="h-2.5 w-2.5 rounded-full bg-white/60 ring-1 ring-black/10 data-[active=true]:bg-white data-[active=true]:w-6 transition-[width,background-color] duration-300" data-idx="{{ $i }}"></button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kamar -->
    <section id="kamar" class="py-16 sm:py-24 scroll-mt-24 bg-sky-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Kamar Tersedia</h2>
            </div>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse(($kamar ?? []) as $item)
                    <div class="rounded-xl border border-sky-100 bg-white shadow-sm overflow-hidden transform-gpu transition duration-300 ease-out hover:scale-[1.02] hover:shadow-md hover:border-sky-200">
                        @if(($item->fotos ?? collect())->isNotEmpty())
                            <div class="h-40 bg-slate-100">
                                <img src="{{ asset('storage/'.$item->fotos->first()->path) }}" alt="{{ $item->nama_kamar }}" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ asset('storage/login/login reg page.webp') }}'">
                            </div>
                        @else
                            <div class="h-40 bg-slate-100 flex items-center justify-center text-slate-400">Foto</div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-lg font-medium text-slate-900">{{ $item->nama_kamar }}</h3>
                            <div class="mt-2">
                                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200">{{ $item->tipe_kamar ?: 'Tipe Standar' }}</span>
                            </div>
                            <p class="mt-2 font-semibold text-slate-900">Rp {{ number_format($item->harga, 0, ',', '.') }}/malam</p>
                            <div class="mt-4">
                                <a href="{{ route('kamar.show', $item->id) }}" class="ui-btn-primary">Pesan Sekarang</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-slate-600">Belum ada data kamar.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Fasilitas with icons -->
    <section id="fasilitas" class="py-16 bg-white scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Mengapa Memilih Kami</h2>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Lokasi strategis di Ubud</div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Kamar bersih dan nyaman</div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-100 text-cyan-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Harga bersahabat &amp; transparan</div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-orange-100 text-orange-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" />
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Wi‑Fi cepat dan stabil</div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-yellow-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Staf ramah dan responsif</div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6 flex items-start gap-4 transition duration-300 ease-out hover:shadow-md hover:-translate-y-0.5">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                        </svg>
                    </span>
                    <div class="mt-0.5">
                        <div class="text-base font-semibold text-slate-900">Proses booking mudah</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section id="galeri" class="py-16 scroll-mt-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Galeri</h2>
            <div class="mt-8 rounded-2xl bg-white ring-1 ring-slate-100 p-4 sm:p-5">
                <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 [column-fill:_balance]">
                    @forelse(($galeri ?? []) as $i => $g)
                        @php($shapes = ['aspect-[4/3]','aspect-[1/1]','aspect-[3/4]','aspect-[16/9]','aspect-[4/5]','aspect-[5/4]','aspect-[3/2]','aspect-[2/3]'])
                        @php($shape = $shapes[$i % count($shapes)])
                        @php($p = trim((string)($g->path ?? '')))
                        @continue($p === '')
                        @php($isExternal = \Illuminate\Support\Str::startsWith($p, ['http://','https://']))
                        <div class="mb-4 break-inside-avoid">
                            <div class="group relative overflow-hidden rounded-lg border bg-slate-100 transform-gpu transition duration-300 ease-out hover:scale-[1.02] hover:shadow-md cursor-pointer {{ $shape }}">
                                @php($src = $isExternal ? $p : asset('storage/'.$p))
                                <img src="{{ $src }}" alt="{{ $g->title }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ asset('storage/login/login reg page.webp') }}'">
                                <div class="pointer-events-none absolute inset-0 bg-slate-900/0 transition-colors duration-200 group-hover:bg-slate-900/10"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-slate-600">Belum ada foto.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- Kontak / Lokasi -->
    <section id="kontak" class="py-16 bg-slate-900 text-white scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 sm:grid-cols-2">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold">Kontak</h2>
                <div class="mt-4 space-y-3 text-slate-200">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
</svg>

                        <span>{{ $contact_address ?? 'Ubud, Bali — Indonesia' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
</svg>

                        <a href="tel:{{ preg_replace('/[^0-9+]/','', $contact_phone ?? '') }}" class="text-white underline underline-offset-2 decoration-white/30 hover:decoration-white">
                            {{ $contact_phone ?? '' }}
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-6.75 4.05a2.25 2.25 0 0 1-2.31 0l-6.75-4.05a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <a href="mailto:{{ $contact_email ?? '' }}" class="text-white underline underline-offset-2 decoration-white/30 hover:decoration-white">
                            {{ $contact_email ?? '' }}
                        </a>
                    </div>
                </div>
                <div class="mt-6">
                    <a href="https://wa.me/{{ preg_replace('/\D/','',$contact_phone ?? '') }}" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-4 py-2 text-sm font-medium hover:bg-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
</svg>

                        Chat WhatsApp
                    </a>
                </div>
            </div>
            <div class="rounded-xl overflow-hidden border border-white/10 bg-white/5">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.753116128303!2d115.27186241092018!3d-8.523333586304394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd23d785ddd5123%3A0x7ac9e07d60509078!2sPondok%20Teges%20(Bagas)!5e0!3m2!1sen!2sid!4v1761204501018!5m2!1sen!2sid"
                    width="600" height="450" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                    class="w-full h-[320px] sm:h-[380px] lg:h-[450px] block"
                ></iframe>
            </div>
        </div>
    </section>

    
    <script>
    // Simple hero carousel without external deps
    document.addEventListener('DOMContentLoaded', () => {
      const root = document.getElementById('heroCarousel');
      if (!root) return;
      const slides = Array.from(root.querySelectorAll(':scope > [data-slide]'));
      if (slides.length === 0) return;
      const prevBtn = root.querySelector(':scope > button:nth-of-type(1)');
      const nextBtn = root.querySelector(':scope > button:nth-of-type(2)');
      const dots = Array.from(root.querySelectorAll('[data-idx]'));
      let i = 0, timer = null, hover = false;
      const setActive = (idx) => {
        i = (idx + slides.length) % slides.length;
        slides.forEach((el, k) => {
          if (k === i) { el.classList.remove('opacity-0','pointer-events-none'); el.classList.add('opacity-100'); }
          else { el.classList.add('opacity-0','pointer-events-none'); el.classList.remove('opacity-100'); }
        });
        dots.forEach((d,k)=> d.setAttribute('data-active', String(k===i)) );
      };
      const next = () => setActive(i + 1);
      const prev = () => setActive(i - 1);
      const start = () => { if (timer) clearInterval(timer); timer = setInterval(()=>{ if(!hover) next(); }, 5000); };
      prevBtn && prevBtn.addEventListener('click', prev);
      nextBtn && nextBtn.addEventListener('click', next);
      dots.forEach(btn => btn.addEventListener('click', () => setActive(parseInt(btn.dataset.idx))));
      root.addEventListener('mouseenter', ()=>{ hover = true; });
      root.addEventListener('mouseleave', ()=>{ hover = false; });
      setActive(0);
      start();
    });
    </script>
    <script>
    // Lightweight Galeri Lightbox (no external libs)
    document.addEventListener('DOMContentLoaded', () => {
      const imgs = Array.from(document.querySelectorAll('#galeri img'));
      if (imgs.length === 0) return;

      const sources = imgs.map(img => img.getAttribute('src'));
      let idx = 0;
      let open = false;

      const overlay = document.createElement('div');
      overlay.className = 'fixed inset-0 z-[60] bg-black/90 opacity-0 pointer-events-none transition-opacity duration-200';
      overlay.setAttribute('role', 'dialog');
      overlay.setAttribute('aria-modal', 'true');

      overlay.innerHTML = `
        <div class="absolute inset-0 grid place-items-center p-4">
          <img class="max-h-[85vh] max-w-[90vw] object-contain rounded-lg shadow-2xl" alt="Galeri" />
          <button type="button" aria-label="Tutup" class="absolute top-4 right-4 rounded-full bg-white/90 hover:bg-white p-2 shadow">
            <svg class="h-5 w-5 text-slate-800" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.222 4.222a.75.75 0 011.06 0L10 8.94l4.718-4.718a.75.75 0 111.06 1.06L11.06 10l4.718 4.718a.75.75 0 11-1.06 1.06L10 11.06l-4.718 4.718a.75.75 0 11-1.06-1.06L8.94 10 4.222 5.282a.75.75 0 010-1.06z" clip-rule="evenodd"/></svg>
          </button>
          <button type="button" aria-label="Sebelumnya" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow hidden sm:inline-flex">
            <svg class="h-5 w-5 text-slate-800" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.78 4.22a.75.75 0 010 1.06L8.06 10l4.72 4.72a.75.75 0 11-1.06 1.06l-5.25-5.25a.75.75 0 010-1.06l5.25-5.25a.75.75 0 011.06 0z" clip-rule="evenodd"/></svg>
          </button>
          <button type="button" aria-label="Berikutnya" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow hidden sm:inline-flex">
            <svg class="h-5 w-5 text-slate-800" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.22 15.78a.75.75 0 010-1.06L11.94 10 7.22 5.28a.75.75 0 111.06-1.06l5.25 5.25a.75.75 0 010 1.06l-5.25 5.25a.75.75 0 01-1.06 0z" clip-rule="evenodd"/></svg>
          </button>
        </div>
      `;

      document.body.appendChild(overlay);
      const imgEl = overlay.querySelector('img');
      const btnClose = overlay.querySelector('button[aria-label="Tutup"]');
      const btnPrev = overlay.querySelector('button[aria-label="Sebelumnya"]');
      const btnNext = overlay.querySelector('button[aria-label="Berikutnya"]');

      const setSrc = (i) => {
        idx = (i + sources.length) % sources.length;
        imgEl.src = sources[idx];
      };

      const show = (i) => {
        open = true;
        setSrc(i);
        overlay.classList.remove('pointer-events-none','opacity-0');
        overlay.classList.add('opacity-100');
        document.documentElement.classList.add('overflow-hidden');
      };
      const hide = () => {
        open = false;
        overlay.classList.add('opacity-0');
        overlay.classList.remove('opacity-100');
        document.documentElement.classList.remove('overflow-hidden');
        setTimeout(()=> overlay.classList.add('pointer-events-none'), 200);
      };

      imgs.forEach((im, i) => {
        im.style.cursor = 'zoom-in';
        im.addEventListener('click', () => show(i));
      });

      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) hide();
      });
      btnClose.addEventListener('click', hide);
      btnPrev.addEventListener('click', () => setSrc(idx - 1));
      btnNext.addEventListener('click', () => setSrc(idx + 1));

      window.addEventListener('keydown', (e) => {
        if (!open) return;
        if (e.key === 'Escape') hide();
        else if (e.key === 'ArrowRight') setSrc(idx + 1);
        else if (e.key === 'ArrowLeft') setSrc(idx - 1);
      });
    });
    </script>
</x-layouts.public>
