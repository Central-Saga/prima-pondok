<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800 overflow-hidden">
                @php
                    $authImage = \App\Models\Setting::get('auth_left_image');
                @endphp
                <div class="absolute inset-0">
                    <img src="{{ $authImage ? asset($authImage) : asset('images/PYS08178.JPG') }}" alt="" class="h-full w-full object-cover">
                </div>
                <div class="absolute inset-0 bg-neutral-900/60"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-24 w-24 items-center justify-center rounded-md">
                        <img src="{{ asset('images/1.png') }}" alt="Logo" class="h-24 w-24 object-contain" />
                    </span>
                    {{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}
                </a>

                @php
                    [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
                @endphp

                {{-- <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-2">
                        <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
                        <footer><flux:heading>{{ trim($author) }}</flux:heading></footer>
                    </blockquote>
                </div> --}}
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-20 w-20 items-center justify-center rounded-md">
                            <img src="{{ asset('images/1.png') }}" alt="Logo" class="h-20 w-20 object-contain" />
                        </span>

                        <span class="sr-only">{{ \App\Models\Setting::get('site_name', 'Pondok Teges') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
