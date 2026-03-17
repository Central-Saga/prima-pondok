<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    public Kamar $kamar;
    public int $activeIndex = 0;
    public ?string $tanggal_checkin = null;
    public ?string $tanggal_checkout = null;
    public int $jumlah_hari = 1;
    public float $total_bayar = 0.0;
    public string $messageText = '';
    public bool $showCalendar = false;
    public int $currentMonth;
    public int $currentYear;
    public array $bookedDates = [];
    public ?int $rescheduleId = null;

    private function statusMessage(): string
    {
        if ($this->kamar->isUnavailable()) {
            return __('rooms.error_room_unavailable');
        }
        if ($this->kamar->isMaintenance()) {
            return __('rooms.error_room_maintenance');
        }
        return '';
    }

    public function mount(Kamar $kamar): void
    {
        $this->kamar = $kamar->load(['fasilitas']);
        $this->activeIndex = 0;
        $this->messageText = $this->statusMessage();
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->rescheduleId = request()->query('reschedule_id');
        
        if ($this->rescheduleId) {
            $booking = Pemesanan::find($this->rescheduleId);
            if ($booking) {
                $this->tanggal_checkin = $booking->tanggal_checkin->format('Y-m-d');
                $this->tanggal_checkout = $booking->tanggal_checkout->format('Y-m-d');
                $this->currentMonth = $booking->tanggal_checkin->month;
                $this->currentYear = $booking->tanggal_checkin->year;
                $this->recalc();
            }
        }
        
        $this->loadBookedDates();
    }

    private function loadBookedDates(): void
    {
        $startOfMonth = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
        
        $bookings = Pemesanan::where('kamar_id', $this->kamar->id)
            ->where(function ($q) {
                $q->whereIn('status', [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CONFIRMED]);
                
                if ($this->rescheduleId) {
                    $q->orWhere(function($subq) {
                        $subq->where('id', $this->rescheduleId)
                             ->where('status', Pemesanan::STATUS_WAITING);
                    });
                }
            })
            ->when($this->rescheduleId, fn($q) => $q->where('id', '!=', $this->rescheduleId))
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->where('tanggal_checkin', '<=', $endOfMonth)
                  ->where('tanggal_checkout', '>=', $startOfMonth);
            })
            ->get();
        
        $this->bookedDates = [];
        foreach ($bookings as $booking) {
            $start = \Carbon\Carbon::parse($booking->tanggal_checkin);
            $end = \Carbon\Carbon::parse($booking->tanggal_checkout);
            
            $current = $start->copy();
            while ($current->lt($end)) {
                if ($current->month === $this->currentMonth && $current->year === $this->currentYear) {
                    $this->bookedDates[] = $current->day;
                }
                $current->addDay();
            }
        }
    }

    public function openCalendar(): void
    {
        $this->showCalendar = true;
        $this->loadBookedDates();
    }

    public function closeCalendar(): void
    {
        $this->showCalendar = false;
    }

    public function resetDates(): void
    {
        $this->tanggal_checkin = null;
        $this->tanggal_checkout = null;
        $this->jumlah_hari = 1;
        $this->total_bayar = 0.0;
        $this->messageText = '';
    }

    public function previousMonth(): void
    {
        $date = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadBookedDates();
    }

    public function nextMonth(): void
    {
        $date = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentMonth = $date->month;
        $this->currentYear = $date->year;
        $this->loadBookedDates();
    }

    public function selectDate(int $day): void
    {
        if (in_array($day, $this->bookedDates)) return;
        $selectedDate = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day);
        if ($selectedDate->isPast() && !$selectedDate->isToday()) return;

        if (!$this->tanggal_checkin || ($this->tanggal_checkin && $this->tanggal_checkout)) {
            $this->tanggal_checkin = $selectedDate->format('Y-m-d');
            $this->tanggal_checkout = null;
        } else {
            $checkin = \Carbon\Carbon::parse($this->tanggal_checkin);
            if ($selectedDate->isAfter($checkin)) {
                $this->tanggal_checkout = $selectedDate->format('Y-m-d');
                $this->recalc();
            } else {
                $this->tanggal_checkin = $selectedDate->format('Y-m-d');
                $this->tanggal_checkout = null;
            }
        }
    }

    public function updated($field): void
    {
        if (in_array($field, ['tanggal_checkin','tanggal_checkout'])) {
            $this->recalc();
        }
    }

    private function recalc(): void
    {
        $this->messageText = $this->statusMessage();
        $this->jumlah_hari = 1;
        $this->total_bayar = 0;
        if ($this->messageText) return;
        if (!$this->tanggal_checkin || !$this->tanggal_checkout) return;

        $start = \Carbon\Carbon::parse($this->tanggal_checkin)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal_checkout)->startOfDay();
        if ($end->lte($start)) {
            $this->messageText = __('rooms.error_checkout_after_checkin');
            return;
        }

        $days = $start->diffInDays($end);
        $this->jumlah_hari = max(1, $days);
        $this->total_bayar = (float) ($this->kamar->harga * $this->jumlah_hari);

        $overlap = Pemesanan::where('kamar_id', $this->kamar->id)
            ->whereIn('status', [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CONFIRMED])
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($x) use ($start, $end) {
                    $x->where('tanggal_checkin', '<', $end)->where('tanggal_checkout', '>', $start);
                });
            })->exists();

        if ($overlap) {
            $this->messageText = __('rooms.error_not_available');
        }
    }

    public function pesan(): void
    {
        $this->messageText = $this->statusMessage();
        if ($this->messageText) {
            $this->addError('tanggal_checkin', $this->messageText);
            return;
        }

        $this->validate(['tanggal_checkin' => 'required|date', 'tanggal_checkout' => 'required|date|after:tanggal_checkin']);
        $this->recalc();
        if ($this->messageText) {
            $this->addError('tanggal_checkin', $this->messageText);
            return;
        }

        $user = Auth::user();
        if (!$user) {
            session()->put('after_login.kamar_url', route('kamar.show', $this->kamar->id, absolute: false));
            $this->redirectRoute('login');
            return;
        }

        if (!$user->hasRole('wisatawan')) {
            try { $user->assignRole('wisatawan'); } catch (\Throwable $e) {}
        }
        if (!$user->wisatawan) {
            $user->wisatawan()->create(['name' => $user->name, 'status' => 'active']);
        }

        if ($this->rescheduleId) {
            $existing = Pemesanan::findOrFail($this->rescheduleId);
            if ($existing->wisatawan_id !== $user->wisatawan->id) {
                abort(403);
            }
            $existing->update([
                'tanggal_checkin' => $this->tanggal_checkin,
                'tanggal_checkout' => $this->tanggal_checkout,
                'jumlah_hari' => $this->jumlah_hari,
                'total_bayar' => $this->total_bayar,
            ]);
            $p = $existing;
        } else {
            $p = Pemesanan::create([
                'wisatawan_id' => $user->wisatawan->id,
                'kamar_id' => $this->kamar->id,
                'tanggal_checkin' => $this->tanggal_checkin,
                'tanggal_checkout' => $this->tanggal_checkout,
                'jumlah_hari' => $this->jumlah_hari,
                'total_bayar' => $this->total_bayar,
                'status' => Pemesanan::STATUS_WAITING,
            ]);
        }

        $this->showCalendar = false;
        $this->redirectRoute('booking.show', $p->id);
    }

    public function select(int $index): void {
        $count = count($this->kamar->fotos ?? []);
        if ($count <= 0) return;
        if ($index < 0) { $this->activeIndex = max(0, $count - 1); return; }
        if ($index >= $count) { $this->activeIndex = 0; return; }
        $this->activeIndex = $index;
    }

    public function nextImage(): void { $this->select($this->activeIndex + 1); }
    public function prevImage(): void { $this->select($this->activeIndex - 1); }

    public function getCalendarDays(): array
    {
        $firstDay = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1);
        $daysInMonth = $firstDay->daysInMonth;
        $startDay = $firstDay->dayOfWeek;
        
        $days = [];
        for ($i = 0; $i < $startDay; $i++) {
            $days[] = null;
        }
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $days[] = $day;
        }
        return $days;
    }

    public function isDayBooked(int $day): bool
    {
        return in_array($day, $this->bookedDates);
    }

    public function isDayPast(int $day): bool
    {
        $cd = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day);
        return $cd->isPast() && !$cd->isToday();
    }

    public function isDaySelected(int $day): bool
    {
        if (!$this->tanggal_checkin) return false;
        $cd = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day);
        return $cd->format('Y-m-d') === $this->tanggal_checkin;
    }

    public function renderDayCell($day): string
    {
        if ($day === null) {
            return '<div></div>';
        }

        $booked = $this->isDayBooked($day);
        $past = $this->isDayPast($day);
        $selected = $this->isDaySelected($day);
        $isCheckout = ($this->tanggal_checkout && \Carbon\Carbon::parse($this->tanggal_checkout)->format('Y-m-d') === \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day)->format('Y-m-d'));
        $isInRange = false;
        
        if ($this->tanggal_checkin && $this->tanggal_checkout) {
            $checkinDate = \Carbon\Carbon::parse($this->tanggal_checkin)->startOfDay();
            $checkoutDate = \Carbon\Carbon::parse($this->tanggal_checkout)->startOfDay();
            $currentDate = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day)->startOfDay();
            $isInRange = $currentDate->isBetween($checkinDate, $checkoutDate) && !$currentDate->equalTo($checkoutDate);
        }

        $disabled = $past || $booked;
        
        $buttonClass = "p-2.5 rounded-lg text-sm font-semibold transition-all duration-200 min-h-[90px] flex flex-col items-center justify-center shadow-sm break-words ";
        if ($past || $booked) {
            $buttonClass .= "bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200";
        } elseif ($selected || $isCheckout) {
            $buttonClass .= "bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md hover:shadow-lg transform hover:scale-105 border-2 border-emerald-400";
        } elseif ($isInRange) {
            $buttonClass .= "bg-emerald-50 text-emerald-800 border-2 border-emerald-200 hover:bg-emerald-100";
        } else {
            $buttonClass .= "bg-white border-2 border-sky-200 text-slate-800 hover:bg-sky-50 hover:border-sky-400 hover:shadow-md transform hover:-translate-y-0.5";
        }
        
        $showPrice = (!$disabled && !$selected && !$isCheckout && !$isInRange) || ($selected || $isCheckout);
        $priceColor = ($selected || $isCheckout) ? 'text-white text-opacity-90' : 'text-sky-600 font-medium';
        $price = number_format($this->kamar->harga, 0, ',', '.');
        
        $html = '<button type="button" wire:click="selectDate(' . $day . ')"';
        if ($disabled) $html .= ' disabled';
        $html .= ' class="' . $buttonClass . '">';
        $html .= '<div class="text-lg font-bold mb-1">' . $day . '</div>';
        if ($showPrice) {
            $html .= '<div class="text-[10px] leading-tight ' . $priceColor . ' text-center">IDR<br>' . $price . '</div>';
        }
        if ($past || $booked) {
            $naParts = explode('|', __('rooms.calendar_not_available_label'));
            $html .= '<div class="text-xs text-gray-400 mt-1 font-semibold">' . ($naParts[0] ?? '') . '<br>' . ($naParts[1] ?? '') . '</div>';
        }
        $html .= '</button>';
        
        return $html;
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-7">
                <div class="relative aspect-[16/9] lg:aspect-[21/9] rounded-xl overflow-hidden border bg-slate-100">
                    @php($fotos = $kamar->fotos ?? [])
                    @php($fotoCount = count($fotos))
                    @if($fotoCount > 0)
                        @php($main = $fotos[$activeIndex] ?? $fotos[0])
                        <img src="{{ asset('storage/'.ltrim($main,'/')) }}" alt="{{ $kamar->nama_kamar }}" class="h-full w-full object-cover" />
                        @if($fotoCount > 1)
                            <button type="button" wire:click="prevImage" class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow">&lsaquo;</button>
                            <button type="button" wire:click="nextImage" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow">&rsaquo;</button>
                        @endif
                    @else
                        <div class="h-full w-full flex items-center justify-center text-slate-400">{{ __('rooms.no_photo_fallback') }}</div>
                    @endif
                </div>

                @if(!empty($kamar->fotos))
                    <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach($kamar->fotos as $i => $path)
                            <button type="button" wire:click="select({{ $i }})" class="aspect-[4/3] rounded-lg overflow-hidden border bg-slate-100 ring-2 transition {{ $activeIndex === $i ? 'ring-sky-400' : 'ring-transparent hover:ring-slate-300' }}">
                                <img src="{{ asset('storage/'.ltrim($path,'/')) }}" alt="{{ __('rooms.no_photo') }} {{ $i+1 }}" class="h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $kamar->nama_kamar }}</h1>
                    <x-status-badge :status="$kamar->getDisplayStatus()" class="text-sm inline-block mt-2" />
                    
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-slate-600">
                        <span>{{ __('rooms.type_label') }} <span class="font-medium text-slate-900">{{ $kamar->tipe_kamar ?: __('rooms.type_default') }}</span></span>
                        <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">Rp {{ number_format($kamar->harga,0,',','.') }} {{ __('rooms.price_badge_suffix') }}</span>
                    </div>

                    @if($kamar->fasilitas->isNotEmpty())
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('rooms.facilities_title') }}</h2>
                            @php($colors = ['emerald','sky','amber','violet','rose','indigo'])
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($kamar->fasilitas as $f)
                                    @php($c = $colors[$loop->index % count($colors)])
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset {{ 'bg-'.$c.'-50 text-'.$c.'-700 ring-'.$c.'-200' }}">{{ $f->nama }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($kamar->deskripsi)
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-slate-900">{{ __('rooms.description_title') }}</h2>
                            <div class="mt-2 whitespace-pre-line text-slate-700 leading-relaxed text-justify">{{ $kamar->deskripsi }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-5">
                @php($isMaintenance = $kamar->isMaintenance())
                @php($isUnavailable = $kamar->isUnavailable())
                @php($bookingDisabled = $isMaintenance || $isUnavailable)
                <div id="booking" class="mt-2 lg:mt-0 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm lg:sticky lg:top-24">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('rooms.book_card_title') }}</h3>
                            <p class="mt-1 text-sm text-slate-600">{{ __('rooms.book_card_subtitle') }}</p>
                        </div>
                        <x-status-badge :status="$kamar->getDisplayStatus()" />
                    </div>

                    @if($bookingDisabled)
                    <div class="mt-4 rounded-lg p-4 bg-amber-50 border border-amber-200">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900">{{ $isMaintenance ? __('rooms.status_maintenance_title') : __('rooms.status_unavailable_title') }}</h4>
                                <p class="mt-1 text-sm text-amber-700">{{ $messageText ?: __('rooms.error_room_not_available') }}</p>
                                <p class="mt-2 text-sm text-amber-600">{{ __('rooms.contact_admin_help') }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mt-4">
                        <button type="button" wire:click="openCalendar" class="w-full rounded-lg border-2 border-dashed border-sky-300 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-700 hover:bg-sky-100 transition-colors">
                            <svg class="inline-block h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0121 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            {{ __('rooms.choose_date_calendar') }}
                        </button>
                    </div>
                    @endif

                    <!-- Calendar Section (Non-Modal) -->
                    @if($showCalendar)
                    <div class="mt-6 p-6 border-2 border-sky-200 rounded-xl bg-gradient-to-br from-white to-sky-50 shadow-lg">
                        <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-sky-100">
                            <button type="button" wire:click="previousMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">‹</button>
                            <h3 class="text-xl font-bold text-slate-800">{{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$currentMonth - 1] }} {{ $currentYear }}</h3>
                            <button type="button" wire:click="nextMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">›</button>
                        </div>

                        <div class="grid grid-cols-7 gap-2 mb-6">
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                                <div class="text-center font-bold text-slate-600 py-2 text-xs uppercase tracking-wide">{{ $dayName }}</div>
                            @endforeach

                            @foreach($this->getCalendarDays() as $day)
                                {!! $this->renderDayCell($day) !!}
                            @endforeach
                        </div>

                        @if($tanggal_checkin)
                            <div class="bg-gradient-to-r from-emerald-50 to-sky-50 p-5 rounded-xl border-2 border-emerald-200 mb-4 shadow-md">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="font-bold text-slate-800">{{ __('rooms.booking_summary') }}</h4>
                                </div>
                                <p class="text-sm text-slate-700"><strong class="text-emerald-700">{{ __('booking.checkin') }}:</strong> {{ $tanggal_checkin }}</p>
                                @if($tanggal_checkout)
                                    <p class="text-sm text-slate-700 mt-1"><strong class="text-emerald-700">{{ __('booking.checkout') }}:</strong> {{ $tanggal_checkout }}</p>
                                    <div class="mt-3 pt-3 border-t border-emerald-200">
                                        <p class="text-sm text-slate-700"><strong class="text-sky-700">{{ __('rooms.nights_label') }}:</strong> {{ $jumlah_hari }} {{ trans_choice('booking.nights', $jumlah_hari) }}</p>
                                        <p class="text-base font-bold text-slate-900 mt-1"><strong class="text-sky-700">{{ __('booking.total') }}:</strong> Rp {{ number_format($total_bayar, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="mt-4 pt-4 border-t border-emerald-200 flex gap-3">
                                        <button type="button" wire:click="pesan" class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ __('rooms.book_now') }}
                                        </button>
                                        <button type="button" wire:click="resetDates" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:from-slate-600 hover:to-slate-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            {{ __('rooms.reset_dates') }}
                                        </button>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-500 mt-2 italic">{{ __('rooms.select_checkout_date') }}</p>
                                @endif
                            </div>
                        @endif

                        @if($messageText)
                            <div class="rounded-md bg-rose-50 text-rose-700 p-3 text-sm mb-4">{{ $messageText }}</div>
                        @endif

                        <button type="button" wire:click="closeCalendar" class="w-full bg-gradient-to-r from-slate-500 to-slate-600 text-white font-semibold py-3 rounded-lg hover:from-slate-600 hover:to-slate-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('rooms.close_calendar') }}
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
