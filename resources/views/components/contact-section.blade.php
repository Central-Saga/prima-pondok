@php
    $title = $title ?? __('landing.contact_section_title');
    $address = $address ?? \App\Models\Setting::get('contact_address', 'Ubud, Bali — Indonesia');
    $phone = $phone ?? \App\Models\Setting::get('contact_phone', '+62-812-0000-0000');
    $email = $email ?? \App\Models\Setting::get('contact_email', 'info@pondokteges.local');
    $mapSrc = $mapSrc ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3945.753116128303!2d115.27186241092018!3d-8.523333586304394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd23d785ddd5123%3A0x7ac9e07d60509078!2sPondok%20Teges%20(Bagas)!5e0!3m2!1sen!2sid!4v1761204501018!5m2!1sen!2sid';
    $wa = preg_replace('/\D/','', $phone);
@endphp

<section {{ $attributes->merge(['class' => 'py-16 bg-slate-900 text-white scroll-mt-24']) }}>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-8 sm:grid-cols-2">
        <div>
            <h2 class="text-2xl sm:text-3xl font-semibold">{{ $title }}</h2>
            <div class="mt-4 space-y-3 text-slate-200">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>{{ $address }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                    <a href="tel:{{ preg_replace('/[^0-9+]/','', $phone) }}" class="text-white underline underline-offset-2 decoration-white/30 hover:decoration-white">{{ $phone }}</a>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="h-5 w-5 text-white/80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-6.75 4.05a2.25 2.25 0 0 1-2.31 0l-6.75-4.05a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <a href="mailto:{{ $email }}" class="text-white underline underline-offset-2 decoration-white/30 hover:decoration-white">{{ $email }}</a>
                </div>
            </div>
            <div class="mt-6">
                <a href="https://wa.me/{{ $wa }}" class="inline-flex items-center gap-2 rounded-md bg-white/10 px-4 py-2 text-sm font-medium hover:bg-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    </svg>
                    {{ __('landing.contact_whatsapp') }}
                </a>
            </div>
        </div>
        <div class="rounded-xl overflow-hidden border border-white/10 bg-white/5">
            <iframe
                src="{{ $mapSrc }}"
                width="600" height="450" style="border:0;"
                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                class="w-full h-[320px] sm:h-[380px] lg:h-[450px] block"
            ></iframe>
        </div>
    </div>
</section>
