<header data-site-header class="sticky top-0 z-40 bg-white/70 backdrop-blur border-b transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 lg:h-24 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight text-slate-900">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-20 lg:h-24 lg:w-24 object-contain shrink-0 self-center" />
            <span>{{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}</span>
        </a>
        <nav class="flex items-center gap-4">
            @php($onHome = request()->routeIs('home'))
            <a href="{{ route('kamar.index') }}" class="text-slate-600 hover:text-slate-900">{{ __('nav.rooms') }}</a>
            <a href="{{ route('about') }}" class="text-slate-600 hover:text-slate-900">{{ __('nav.about') }}</a>
            <a href="{{ $onHome ? '#galeri' : route('home').'#galeri' }}" class="text-slate-600 hover:text-slate-900">{{ __('nav.gallery') }}</a>
            <a href="{{ $onHome ? '#kontak' : route('home').'#kontak' }}" class="text-slate-600 hover:text-slate-900">{{ __('nav.contact') }}</a>

            <div class="flex items-center gap-1 border rounded-full px-1 py-0.5 text-xs">
                @php($locale = app()->getLocale())
                <a href="{{ route('locale.switch', ['locale' => 'id']) }}"
                   class="px-2 py-0.5 rounded-full {{ $locale === 'id' ? 'bg-[#00A6F4] text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    ID
                </a>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                   class="px-2 py-0.5 rounded-full {{ $locale === 'en' ? 'bg-[#00A6F4] text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    EN
                </a>
            </div>
            @auth
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="ui-btn-primary">{{ __('nav.dashboard') }}</a>
                @elserole('wisatawan')
                    <a href="{{ route('kamar.index') }}" class="ui-btn-primary">{{ __('nav.book_room') }}</a>
                    <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('nav.bookings') }}</a>
                @else
                    <a href="{{ route('dashboard') }}" class="ui-btn-primary">{{ __('nav.dashboard') }}</a>
                @endrole
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="ui-btn-secondary">{{ __('nav.logout') }}</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="ui-btn-secondary">{{ __('nav.login') }}</a>
                <a href="{{ route('register') }}" class="ui-btn-primary">{{ __('nav.register') }}</a>
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
