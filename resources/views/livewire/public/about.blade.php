<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
}; ?>

<main>
    <!-- Hero -->
    <section class="relative isolate overflow-hidden bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">{{ __('about.hero_title') }}</h1>
                <p class="mt-6 text-lg leading-8 text-slate-700">
                    {{ __('about.hero_body_1') }}
                </p>
                <div class="mt-8 flex items-center gap-3">
                    <a href="{{ route('kamar.index') }}" class="ui-btn-primary">{{ __('about.hero_view_rooms') }}</a>
                    <a href="{{ route('home') }}#kontak" class="ui-btn-secondary">{{ __('about.hero_contact_us') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Placeholder -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl overflow-hidden border bg-slate-50">
                <div class="relative aspect-[16/9] bg-slate-200 grid place-items-center">
                    <div class="text-center">
                        <button type="button" class="mx-auto mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-white/90 text-slate-900 shadow ring-1 ring-slate-200">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.14v13.72L19 12 8 5.14Z"/></svg>
                        </button>
                        <div class="text-slate-700 font-medium">{{ __('about.video_title') }}</div>
                        <div class="text-slate-500 text-sm">{{ __('about.video_subtitle') }}</div>
                    </div>
                </div>
            </div>
        </div>
        </section>

    <!-- Story -->
    <section class="py-12 sm:py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-2 items-start">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('about.story_title') }}</h2>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    {{ __('about.story_p1') }}
                </p>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    {{ __('about.story_p2') }}
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border"></div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border"></div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border"></div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border"></div>
            </div>
        </div>
        </section>

    <!-- Values -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('about.values_title') }}</h2>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">{{ __('about.value_hospitality_title') }}</div>
                    <p class="mt-2 text-slate-700">{{ __('about.value_hospitality_body') }}</p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">{{ __('about.value_cleanliness_title') }}</div>
                    <p class="mt-2 text-slate-700">{{ __('about.value_cleanliness_body') }}</p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">{{ __('about.value_responsiveness_title') }}</div>
                    <p class="mt-2 text-slate-700">{{ __('about.value_responsiveness_body') }}</p>
                </div>
            </div>
        </div>
        </section>

    <!-- Stats + CTA -->
    {{-- <section class="py-12 sm:py-16 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-3xl font-bold">10+</div>
                    <div class="mt-1 text-white/80">Tahun melayani</div>
                </div>
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-3xl font-bold">4.8/5</div>
                    <div class="mt-1 text-white/80">Rata-rata ulasan tamu</div>
                </div>
                <div class="rounded-2xl bg-white/5 ring-1 ring-white/10 p-6">
                    <div class="text-3xl font-bold">Lokasi Strategis</div>
                    <div class="mt-1 text-white/80">Dekat destinasi populer Ubud</div>
                </div>
            </div>
            <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-3">
                <a href="{{ route('kamar.index') }}" class="ui-btn-primary">Pesan Kamar</a>
                <a href="{{ route('home') }}#kontak" class="ui-btn-secondary">Kontak Kami</a>
            </div>
        </div>
        </section> --}}

    <!-- Contact reuse -->
    <x-contact-section />
</main>
