<header data-site-header class="sticky top-0 z-40 bg-white/80 backdrop-blur border-b transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-semibold tracking-tight text-slate-900">Pondok Teges</a>
        <nav class="flex items-center gap-4">
            @php($onHome = request()->routeIs('home'))
            <a href="{{ $onHome ? '#kamar' : route('home').'#kamar' }}" class="text-slate-600 hover:text-slate-900">Kamar</a>
            <a href="{{ $onHome ? '#fasilitas' : route('home').'#fasilitas' }}" class="text-slate-600 hover:text-slate-900">Fasilitas</a>
            <a href="{{ $onHome ? '#galeri' : route('home').'#galeri' }}" class="text-slate-600 hover:text-slate-900">Galeri</a>
            <a href="{{ $onHome ? '#kontak' : route('home').'#kontak' }}" class="text-slate-600 hover:text-slate-900">Kontak</a>
            @auth
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">Dashboard</a>
                @elserole('wisatawan')
                    <a href="{{ route('home') }}#kamar" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">Pesan Kamar</a>
                    <a href="{{ route('booking.index') }}" class="text-slate-600 hover:text-slate-900 text-sm">Booking</a>
                @else
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">Dashboard</a>
                @endrole
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-slate-600 hover:text-slate-900 text-sm">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-slate-600 hover:text-slate-900">Masuk</a>
                <a href="{{ route('register') }}" class="inline-flex items-center rounded-md bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-700">Daftar</a>
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
    </script>
    </header>
