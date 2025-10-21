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
        @php($heroImages = collect($galeri ?? [])->take(6))
        <div id="heroCarousel" class="absolute inset-0">
            @forelse($heroImages as $idx => $g)
                @php($src = \Illuminate\Support\Str::startsWith($g->path, ['http://','https://']) ? $g->path : url('media/'.$g->path))
                <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" data-slide>
                    <img src="{{ $src }}" alt="{{ $g->title ?? 'Galeri' }}" class="h-full w-full object-cover" loading="lazy">
                </div>
            @empty
                <div class="absolute inset-0 bg-slate-100"></div>
            @endforelse
            <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-transparent pointer-events-none"></div>
            <div class="absolute inset-0">
                <div class="mx-auto max-w-7xl h-full px-4 sm:px-6 lg:px-8 relative">
                    
                    @if($heroImages->count() > 1)
                    <div class="absolute bottom-6 inset-x-0 flex items-center justify-center gap-2 z-20">
                        @foreach($heroImages as $i => $g)
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
                <h1 class="text-4xl font-bold tracking-tight text-white drop-shadow-md sm:text-5xl">{{ $hero_title ?? 'Home Stay Pondok Teges' }}</h1>
                <p class="mt-6 text-lg leading-8 text-slate-600">{{ $hero_subtitle ?? 'Rasakan kenyamanan menginap di kawasan Ubud.' }}</p>
                <div class="mt-10 flex items-center gap-x-6">
                    <a href="#kamar" class="rounded-md bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-slate-700">Lihat Kamar</a>
                    <a href="#kontak" class="text-sm font-semibold leading-6 text-slate-900">Kontak Kami →</a>
                </div>
                @php($heroImages = collect($galeri ?? [])->take(6))
                <div class="relative hidden" aria-hidden="true">
                    <div id="heroCarouselHidden" class="absolute inset-0">
                        @forelse($heroImages as $idx => $g)
                            @php($src = \Illuminate\Support\Str::startsWith($g->path, ['http://','https://']) ? $g->path : url('media/'.$g->path))
                            <div class="absolute inset-0 transition-opacity duration-700 ease-in-out {{ $idx === 0 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}" data-slide>
                                <img src="{{ $src }}" alt="{{ $g->title ?? 'Galeri' }}" class="h-full w-full object-cover" loading="lazy">
                            </div>
                        @empty
                            <div class="absolute inset-0 grid place-items-center text-slate-400">Tidak ada gambar</div>
                        @endforelse
                        <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-transparent pointer-events-none"></div>


                        @if($heroImages->count() > 1)
                        <div class="absolute bottom-3 inset-x-0 flex items-center justify-center gap-2 z-20">
                            @foreach($heroImages as $i => $g)
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
    <section id="kamar" class="py-16 sm:py-24 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between">
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Kamar Tersedia</h2>
            </div>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse(($kamar ?? []) as $item)
                    <div class="rounded-xl border bg-white shadow-sm overflow-hidden transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02] hover:shadow-md">
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

    <!-- Fasilitas with icons -->
    <section id="fasilitas" class="py-16 bg-slate-50 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Fasilitas</h2>
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-sm">
                <div class="rounded-xl bg-sky-50 px-4 py-3 ring-1 ring-inset ring-sky-200 text-sky-700 flex items-center gap-3 hover:bg-sky-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z"/>
                    </svg>
                    <span>Wi‑Fi</span>
                </div>
                <div class="rounded-xl bg-amber-50 px-4 py-3 ring-1 ring-inset ring-amber-200 text-amber-800 flex items-center gap-3 hover:bg-amber-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                    <span>Parkir</span>
                </div>
                <div class="rounded-xl bg-cyan-50 px-4 py-3 ring-1 ring-inset ring-cyan-200 text-cyan-800 flex items-center gap-3 hover:bg-cyan-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-cyan-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                    </svg>
                    <span>AC / Kipas</span>
                </div>
                <div class="rounded-xl bg-orange-50 px-4 py-3 ring-1 ring-inset ring-orange-200 text-orange-800 flex items-center gap-3 hover:bg-orange-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z"/>
                    </svg>
                    <span>Air Panas</span>
                </div>
                <div class="rounded-xl bg-yellow-50 px-4 py-3 ring-1 ring-inset ring-yellow-200 text-yellow-800 flex items-center gap-3 hover:bg-yellow-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
                    </svg>
                    <span>Sarapan (opsional)</span>
                </div>
                <div class="rounded-xl bg-emerald-50 px-4 py-3 ring-1 ring-inset ring-emerald-200 text-emerald-800 flex items-center gap-3 hover:bg-emerald-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02]">
                    <svg class="h-5 w-5 shrink-0 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/>
                    </svg>
                    <span>Layanan Kebersihan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section id="galeri" class="py-16 scroll-mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Galeri</h2>
            <div class="mt-8 columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 [column-fill:_balance]">
                @forelse(($galeri ?? []) as $i => $g)
                    @php($shapes = ['aspect-[4/3]','aspect-[1/1]','aspect-[3/4]','aspect-[16/9]','aspect-[4/5]','aspect-[5/4]','aspect-[3/2]','aspect-[2/3]'])
                    @php($shape = $shapes[$i % count($shapes)])
                    <div class="mb-4 break-inside-avoid">
                        <div class="group relative overflow-hidden rounded-lg border bg-slate-100 transform-gpu transition-transform duration-300 ease-out hover:scale-[1.02] hover:shadow-md cursor-pointer {{ $shape }}">
                            @if(\Illuminate\Support\Str::startsWith($g->path, ['http://','https://']))
                                <img src="{{ $g->path }}" alt="{{ $g->title }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                            @else
                                <img src="{{ url('media/'.$g->path) }}" alt="{{ $g->title }}" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-slate-600">Belum ada foto.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Kontak / Lokasi -->
    <section id="kontak" class="py-16 bg-slate-900 text-white scroll-mt-24">
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
</body>
</html>
