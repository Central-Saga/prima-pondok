@php
    $status = strtolower($status ?? ($label ?? ''));

    $paletteByStatus = [
        // umum
        'available' => 'emerald',
        'maintenance' => 'amber',
        'unavailable' => 'rose',

        // pemesanan
        'pending' => 'amber',
        'confirmed' => 'emerald',
        'cancelled' => 'rose',
        'completed' => 'sky',

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
    ];

    $palette = $paletteByStatus[$status] ?? 'slate';
    $classes = $classByPalette[$palette] ?? $classByPalette['slate'];
    $text = $label ?? $status;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset capitalize '.$classes]) }}>
    {{ $text }}
    @isset($slot)
        {{ $slot }}
    @endisset
</span>
