<header data-site-header class="sticky top-0 z-40 bg-slate-950/90 text-white backdrop-blur border-b border-white/10 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 lg:h-24 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 font-semibold tracking-tight text-white">
            <img src="{{ asset('images/1.png') }}" alt="Logo" class="h-20 w-20 lg:h-24 lg:w-24 object-contain shrink-0 self-center" />
            <span>{{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}</span>
        </a>
        <nav class="flex items-center gap-4">
            @php($onHome = request()->routeIs('home'))
            <a href="{{ route('kamar.index') }}" class="text-white/80 hover:text-white">{{ __('nav.rooms') }}</a>
            <a href="{{ route('about') }}" class="text-white/80 hover:text-white">{{ __('nav.about') }}</a>
            <a href="{{ $onHome ? '#galeri' : route('home').'#galeri' }}" class="text-white/80 hover:text-white">{{ __('nav.gallery') }}</a>
            <a href="{{ $onHome ? '#kontak' : route('home').'#kontak' }}" class="text-white/80 hover:text-white">{{ __('nav.contact') }}</a>

            <div class="flex items-center gap-1 border border-white/20 rounded-full px-1 py-0.5 text-xs">
                @php($locale = app()->getLocale())
                <a href="{{ route('locale.switch', ['locale' => 'id']) }}"
                   class="px-2 py-0.5 rounded-full {{ $locale === 'id' ? 'bg-[#00A6F4] text-white' : 'text-white/80 hover:text-white' }}">
                    ID
                </a>
                <a href="{{ route('locale.switch', ['locale' => 'en']) }}"
                   class="px-2 py-0.5 rounded-full {{ $locale === 'en' ? 'bg-[#00A6F4] text-white' : 'text-white/80 hover:text-white' }}">
                    EN
                </a>
            </div>
            @auth
                @role('admin')
                    <a href="{{ route('admin.dashboard') }}" class="ui-btn-primary">{{ __('nav.dashboard') }}</a>
                @elserole('wisatawan')
                    <a href="{{ route('kamar.index') }}" class="ui-btn-primary">{{ __('nav.book_room') }}</a>
                    <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('nav.bookings') }}</a>
                    <details class="relative">
                        <summary class="list-none cursor-pointer ui-btn-secondary" aria-label="{{ __('account.profile_title') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.964 0a9 9 0 10-11.964 0m11.964 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </summary>
                        <div class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white text-slate-800 shadow-lg">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <div class="text-sm font-semibold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('wisatawan.profile') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">{{ __('account.profile_title') }}</a>
                            <a href="{{ route('wisatawan.password') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">{{ __('account.change_password') }}</a>
                            <div class="border-t border-slate-100"></div>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-700 hover:bg-rose-50">{{ __('nav.logout') }}</button>
                            </form>
                        </div>
                    </details>
                @else
                    <a href="{{ route('dashboard') }}" class="ui-btn-primary">{{ __('nav.dashboard') }}</a>
                @endrole
                @unless(auth()->user()->hasRole('wisatawan'))
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="ui-btn-secondary">{{ __('nav.logout') }}</button>
                    </form>
                @endunless
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
        header.classList.toggle('ring-white/10', scrolled);
      };
      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    })();
    </script>
    </header>
