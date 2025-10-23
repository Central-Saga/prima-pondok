<header data-site-header class="sticky top-0 z-40 bg-white/70 backdrop-blur border-b transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 lg:h-24 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight text-slate-900">
            <img src="{{ asset('storage/logo/3.png') }}" alt="Logo" class="h-20 w-20 lg:h-24 lg:w-24 object-contain shrink-0 self-center" />
            <span>{{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}</span>
        </a>
        <nav class="flex items-center gap-4">
            @php($onHome = request()->routeIs('home'))
            <a href="{{ $onHome ? '#kamar' : route('home').'#kamar' }}" class="text-slate-600 hover:text-slate-900">Kamar</a>
            <a href="{{ $onHome ? '#fasilitas' : route('home').'#fasilitas' }}" class="text-slate-600 hover:text-slate-900">Fasilitas</a>
            <a href="{{ $onHome ? '#galeri' : route('home').'#galeri' }}" class="text-slate-600 hover:text-slate-900">Galeri</a>
            <a href="{{ $onHome ? '#kontak' : route('home').'#kontak' }}" class="text-slate-600 hover:text-slate-900">Kontak</a>
            @auth
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="ui-btn-primary">Dashboard</a>
                @elserole('wisatawan')
                    <a href="{{ route('home') }}#kamar" class="ui-btn-primary">Pesan Kamar</a>
                    <a href="{{ route('booking.index') }}" class="ui-btn-secondary">Booking</a>
                @else
                    <a href="{{ route('dashboard') }}" class="ui-btn-primary">Dashboard</a>
                @endrole
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="ui-btn-secondary">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="ui-btn-secondary">Masuk</a>
                <a href="{{ route('register') }}" class="ui-btn-primary">Daftar</a>
            @endauth
        </nav>
    </div>
    <script>
    // Intercept same-page anchor clicks for smooth scroll
    document.addEventListener('click', (e) => {
      const a = e.target.closest('a');
      if (!a || !a.href) return;
      try {
        const url = new URL(a.href, location.href);
        if (!url.hash) return;
        if (url.origin === location.origin && url.pathname === location.pathname) {
          const id = url.hash.slice(1);
          const el = document.getElementById(id);
          if (el) {
            e.preventDefault();
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.replaceState(null, '', '#'+id);
          }
        }
      } catch (_) {}
    }, { passive: false });
    // Subtle tint and ring when page is scrolled
    (function(){
      const header = document.querySelector('[data-site-header]');
      if (!header) return;
      const onScroll = () => {
        const scrolled = window.scrollY > 4;
        header.classList.toggle('shadow-sm', scrolled);
        header.classList.toggle('ring-1', scrolled);
        header.classList.toggle('ring-sky-100', scrolled);
        header.classList.toggle('bg-white/80', scrolled);
        header.classList.toggle('bg-white/70', !scrolled);
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    })();
    </script>
    </header>
