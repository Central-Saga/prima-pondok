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
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl">Tentang Kami</h1>
                <p class="mt-6 text-lg leading-8 text-slate-700">
                    Kami adalah homestay nyaman di kawasan Ubud yang berfokus pada keramahan,
                    kebersihan, dan pengalaman menginap yang menyenangkan bagi para tamu.
                </p>
                <div class="mt-8 flex items-center gap-3">
                    <a href="{{ route('kamar.index') }}" class="ui-btn-primary">Lihat Kamar</a>
                    <a href="{{ route('home') }}#kontak" class="ui-btn-secondary">Hubungi Kami</a>
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
                        <div class="text-slate-700 font-medium">Placeholder Video</div>
                        <div class="text-slate-500 text-sm">Area ini disiapkan untuk menaruh video profil.</div>
                    </div>
                </div>
            </div>
        </div>
        </section>

    <!-- Story -->
    <section class="py-12 sm:py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-2 items-start">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Cerita Kami</h2>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    Berawal dari rumah keluarga yang hangat, kami menghadirkan suasana menginap yang
                    tenang dan bersahaja. Setiap kamar dirancang agar tamu dapat beristirahat dengan nyaman
                    setelah menjelajahi keindahan Ubud. Kami percaya bahwa pengalaman kecil—senyuman tulus,
                    kamar bersih, dan respons cepat—membuat tinggal Anda terasa berkesan.
                </p>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    Kami terus berbenah dan mendengarkan masukan tamu untuk menciptakan pengalaman yang
                    lebih baik dari waktu ke waktu.
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
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900">Nilai Utama</h2>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">Keramahan</div>
                    <p class="mt-2 text-slate-700">Melayani dengan hati dan senyuman untuk setiap tamu.</p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">Kebersihan</div>
                    <p class="mt-2 text-slate-700">Menjaga standar kebersihan menyeluruh di setiap kamar.</p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold">Kecepatan Respons</div>
                    <p class="mt-2 text-slate-700">Tanggap terhadap kebutuhan dan pertanyaan tamu.</p>
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
