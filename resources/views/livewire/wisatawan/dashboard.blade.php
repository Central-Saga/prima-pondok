<?php

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $pemesanan;
    public $activeBooking = null;
    public bool $showWarning = false;
    public bool $showOverdue = false;
    public string $warningType = ''; // 'approaching', 'overdue'

    public function mount(): void
    {
        $user = Auth::user();
        $wisatawan = $user?->wisatawan;
        $this->pemesanan = $wisatawan
            ? Pemesanan::with('kamar')->where('wisatawan_id', $wisatawan->id)->latest()->take(10)->get()
            : collect();

        // Find active confirmed booking that needs checkout alert
        if ($wisatawan) {
            $this->activeBooking = Pemesanan::with('kamar')
                ->where('wisatawan_id', $wisatawan->id)
                ->where('status', Pemesanan::STATUS_CONFIRMED)
                ->where('tanggal_checkout', '<=', now()->toDateString())
                ->first();

            // Also check for checkout day approaching (today is checkout day)
            if (!$this->activeBooking) {
                $this->activeBooking = Pemesanan::with('kamar')
                    ->where('wisatawan_id', $wisatawan->id)
                    ->where('status', Pemesanan::STATUS_CONFIRMED)
                    ->where('tanggal_checkout', '=', now()->toDateString())
                    ->first();
            }

            // Check for overdue - past checkout time
            if (!$this->activeBooking) {
                // Look for confirmed bookings where checkout date has passed
                $this->activeBooking = Pemesanan::with('kamar')
                    ->where('wisatawan_id', $wisatawan->id)
                    ->where('status', Pemesanan::STATUS_CONFIRMED)
                    ->where('tanggal_checkout', '<', now()->toDateString())
                    ->first();
            }

            if ($this->activeBooking) {
                if ($this->activeBooking->isCheckoutOverdue()) {
                    $this->warningType = 'overdue';
                    $this->showOverdue = true;
                } elseif ($this->activeBooking->isCheckoutWarning()) {
                    $this->warningType = 'approaching';
                    $this->showWarning = true;
                }
            }
        }
    }
}; ?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- OVERDUE ALERT - Big warning when past checkout deadline --}}
    @if($showOverdue && $activeBooking)
        <div class="mb-8 rounded-2xl border-2 border-rose-300 bg-rose-50 p-6 shadow-lg animate-pulse" id="overdue-alert">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-rose-800">⚠️ {{ __('booking.overdue_title') }}</h2>
                    <div class="mt-3 rounded-lg bg-white p-4 border border-rose-200">
                        <p class="text-base text-rose-900 font-semibold leading-relaxed">
                            {{ __('booking.overdue_message') }}
                            <span class="text-xl font-bold text-rose-700">Rp {{ number_format(\App\Models\Pemesanan::DENDA_AMOUNT, 0, ',', '.') }}</span>{{ __('booking.overdue_message_suffix') }}
                        </p>
                    </div>
                    <div class="mt-3 text-sm text-rose-700">
                        <p><strong>{{ __('booking.overdue_room') }}:</strong> {{ $activeBooking->kamar->nama_kamar }}</p>
                        <p><strong>{{ __('booking.overdue_checkout_date') }}:</strong> {{ $activeBooking->tanggal_checkout->format('d M Y') }} - 11:00 AM</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($activeBooking->canExtend(1))
                            <a href="{{ route('booking.extend', $activeBooking->id) }}" class="inline-flex items-center rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-500 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('booking.extend_stay') }}
                            </a>
                        @else
                            <button type="button" class="inline-flex items-center rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-500 shadow-md"
                                    onclick="document.getElementById('extend-unavailable-modal').classList.remove('hidden')">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('booking.extend_stay') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- WARNING ALERT - 1 hour before checkout --}}
    @if($showWarning && $activeBooking && !$showOverdue)
        <div class="mb-8 rounded-2xl border-2 border-amber-300 bg-amber-50 p-6 shadow-lg" id="warning-alert">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-amber-800">⏰ {{ __('booking.warning_title') }}</h2>
                    <div class="mt-3 rounded-lg bg-white p-4 border border-amber-200">
                        <p class="text-base text-amber-900 font-medium leading-relaxed">
                            {{ __('booking.warning_message') }} <strong class="text-amber-700">{{ __('booking.warning_message_time') }}</strong> {{ __('booking.warning_message_suffix') }}
                        </p>
                    </div>
                    <div class="mt-3 text-sm text-amber-700">
                        <p><strong>{{ __('booking.warning_room') }}:</strong> {{ $activeBooking->kamar->nama_kamar }}</p>
                        <p><strong>{{ __('booking.warning_checkout_limit') }}:</strong> {{ $activeBooking->tanggal_checkout->format('d M Y') }} - 11:00 AM</p>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($activeBooking->canExtend(1))
                            <a href="{{ route('booking.extend', $activeBooking->id) }}" class="inline-flex items-center rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-500 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('booking.extend_stay') }}
                            </a>
                        @else
                            <button type="button" class="inline-flex items-center rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-500 shadow-md"
                                    onclick="document.getElementById('extend-unavailable-modal').classList.remove('hidden')">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('booking.extend_stay') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL: Extend tidak tersedia --}}
    <div id="extend-unavailable-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/50" onclick="this.parentElement.classList.add('hidden')"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-center">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
                <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900">{{ __('booking.extend_unavailable_title') }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ __('booking.extend_unavailable_message') }}</p>
            <button type="button" onclick="this.closest('#extend-unavailable-modal').classList.add('hidden')" class="mt-5 w-full rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-500">{{ __('booking.extend_unavailable_ok') }}</button>
        </div>
    </div>

    <h1 class="text-2xl font-semibold text-slate-900">{{ __('account.dashboard_title') }}</h1>

    <div class="mt-6">
        <a href="{{ route('kamar.index') }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">{{ __('account.find_rooms') }}</a>
        <a href="{{ route('wisatawan.profile') }}" class="ml-2 inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">{{ __('account.profile_button') }}</a>
        <a href="{{ route('wisatawan.password') }}" class="ml-2 inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">{{ __('account.change_password') }}</a>
    </div>

    <div class="mt-8">
        <h2 class="text-lg font-medium">{{ __('account.recent_bookings') }}</h2>
        <div class="mt-4 divide-y rounded-xl border bg-white">
            @forelse($pemesanan as $p)
                <div class="p-4 flex items-center justify-between">
                    <div class="text-sm">
                        <div class="font-medium text-slate-900">{{ $p->kamar->nama_kamar }}</div>
                        <div class="text-slate-600">{{ $p->tanggal_checkin->format('d M Y') }} → {{ $p->tanggal_checkout->format('d M Y') }} ({{ $p->jumlah_hari }} {{ trans_choice('booking.nights', $p->jumlah_hari) }})</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold">Rp {{ number_format($p->total_bayar,0,',','.') }}</div>
                        <div class="mt-1"><x-status-badge :status="$p->status" /></div>
                        <a href="{{ route('booking.show', $p->id) }}" class="block mt-2 text-xs text-sky-700 hover:underline">{{ __('booking.view_detail') }}</a>
                    </div>
                </div>
            @empty
                <div class="p-4 text-slate-600 text-sm">{{ __('booking.no_bookings') }}</div>
            @endforelse
        </div>
    </div>
</section>
