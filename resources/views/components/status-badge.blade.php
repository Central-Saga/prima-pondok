@php
    $status = strtolower($status ?? ($label ?? ''));

    $paletteByStatus = [
        // umum
        'available' => 'emerald',
        'maintenance' => 'amber',
        'unavailable' => 'rose',

        // pemesanan
        'waiting' => 'sky',
        'pending' => 'amber',
        'confirmed' => 'emerald',
        'cancelled' => 'rose',
        'completed' => 'sky',
        'extend' => 'violet',

        // checkout status (admin)
        'diperingati' => 'amber',
        'jatuh_tempo' => 'rose',

        // pembayaran
        'verified' => 'emerald',
        'rejected' => 'rose',

        // users & roles
        'admin' => 'sky',
        'wisatawan' => 'emerald',

        // lainnya
        'active' => 'emerald',
        'hidden' => 'slate',
    ];

    $classByPalette = [
        'emerald' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'slate' => 'bg-slate-50 text-slate-700 ring-slate-200',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-200',
    ];

    // Translasi status kamar jika ada
    $statusTranslations = [
        'available' => __('rooms.status_available'),
        'maintenance' => __('rooms.status_maintenance'),
        'unavailable' => __('rooms.status_unavailable'),
    ];

    $palette = $paletteByStatus[$status] ?? 'slate';
    $classes = $classByPalette[$palette] ?? $classByPalette['slate'];
    $text = $label ?? $statusTranslations[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset capitalize '.$classes]) }}>
    {{ $text }}
    @isset($slot)
        {{ $slot }}
    @endisset
</span>
