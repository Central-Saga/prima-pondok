<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Bank;
use App\Models\KamarMaintenance;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.public')] class extends Component {
    use WithFileUploads;

    public Pemesanan $pemesanan;
    public ?string $tanggal_checkout_baru = null;
    public int $jumlah_hari_extend = 1;
    public float $total_extend = 0.0;
    public string $messageText = '';
    public bool $showCalendar = false;
    public int $currentMonth;
    public int $currentYear;
    public array $bookedDates = [];
    public array $maintenanceDates = [];

    public ?string $metode_bayar = 'transfer';
    public ?float $jumlah = null;
    public $bukti; // file
    public array $banks = [];
    public bool $submitted = false;
    public ?int $extendBookingId = null;

    public function mount(Pemesanan $pemesanan): void
    {
        $user = Auth::user();
        $wisatawanId = $user->wisatawan?->id;
        abort_if(!$wisatawanId || $pemesanan->wisatawan_id !== $wisatawanId, 403);
        abort_if(!in_array($pemesanan->status, [Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_EXTEND]), 403);

        // Block access if room is not available for extend (next day has booking/maintenance)
        if (!$pemesanan->canExtend(1)) {
            session()->flash('extend_blocked', __('booking.extend_blocked_message'));
            $this->redirect(route('booking.show', $pemesanan->id));
            return;
        }

        $this->pemesanan = $pemesanan->load('kamar');
        $checkoutDate = $pemesanan->tanggal_checkout;
        $this->currentMonth = $checkoutDate->month;
        $this->currentYear = $checkoutDate->year;
        $this->banks = Bank::query()->where('status', 'active')->orderBy('nama_bank')->get(['nama_bank', 'no_transfer'])->toArray();

        $this->loadBookedDates();
    }

    private function loadBookedDates(): void
    {
        $startOfMonth = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $bookings = Pemesanan::where('kamar_id', $this->pemesanan->kamar_id)
            ->where('id', '!=', $this->pemesanan->id)
            ->whereIn('status', [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_EXTEND])
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

        // Load maintenance dates
        $maintenances = $this->pemesanan->kamar->maintenances()
            ->where('tanggal_mulai', '<=', $endOfMonth)
            ->where('tanggal_selesai', '>=', $startOfMonth)
            ->get();

        $this->maintenanceDates = [];
        foreach ($maintenances as $mt) {
            $mStart = \Carbon\Carbon::parse($mt->tanggal_mulai);
            $mEnd = \Carbon\Carbon::parse($mt->tanggal_selesai);
            $current = $mStart->copy();
            while ($current->lte($mEnd)) {
                if ($current->month === $this->currentMonth && $current->year === $this->currentYear) {
                    $this->maintenanceDates[] = $current->day;
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
        if (in_array($day, $this->bookedDates)) {
            $this->messageText = __('booking.extend_room_unavailable');
            return;
        }
        if (in_array($day, $this->maintenanceDates)) {
            $this->messageText = __('booking.extend_room_maintenance');
            return;
        }

        $selectedDate = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day);
        $extendStart = $this->pemesanan->tanggal_checkout->copy();

        // Extend checkout must be after current checkout
        if ($selectedDate->lte($extendStart)) {
            $this->messageText = __('booking.extend_date_after_checkout', ['date' => $extendStart->format('d M Y')]);
            return;
        }

        // Check all dates between current checkout and selected date for availability
        $checkDate = $extendStart->copy();
        while ($checkDate->lt($selectedDate)) {
            $checkDay = $checkDate->day;
            $checkMonth = $checkDate->month;
            $checkYear = $checkDate->year;

            // Load booked dates for this specific month if needed
            $isBooked = Pemesanan::where('kamar_id', $this->pemesanan->kamar_id)
                ->where('id', '!=', $this->pemesanan->id)
                ->whereIn('status', [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CONFIRMED, Pemesanan::STATUS_EXTEND])
                ->where('tanggal_checkin', '<=', $checkDate->toDateString())
                ->where('tanggal_checkout', '>', $checkDate->toDateString())
                ->exists();

            if ($isBooked) {
                $this->messageText = __('booking.extend_booking_conflict', ['date' => $checkDate->format('d M Y')]);
                return;
            }

            $isMaintenance = $this->pemesanan->kamar->maintenances()
                ->where('tanggal_mulai', '<=', $checkDate->toDateString())
                ->where('tanggal_selesai', '>=', $checkDate->toDateString())
                ->exists();

            if ($isMaintenance) {
                $this->messageText = __('booking.extend_maintenance_conflict', ['date' => $checkDate->format('d M Y')]);
                return;
            }

            $checkDate->addDay();
        }

        $this->tanggal_checkout_baru = $selectedDate->format('Y-m-d');
        $this->recalc();
        $this->messageText = '';
    }

    private function recalc(): void
    {
        if (!$this->tanggal_checkout_baru) return;

        $extendStart = $this->pemesanan->tanggal_checkout->copy()->startOfDay();
        $extendEnd = \Carbon\Carbon::parse($this->tanggal_checkout_baru)->startOfDay();
        $days = max(1, $extendStart->diffInDays($extendEnd));

        $this->jumlah_hari_extend = $days;
        $this->total_extend = (float) ($this->pemesanan->kamar->harga * $days);
        $this->jumlah = $this->total_extend;
    }

    public function resetDates(): void
    {
        $this->tanggal_checkout_baru = null;
        $this->jumlah_hari_extend = 1;
        $this->total_extend = 0.0;
        $this->messageText = '';
    }

    public function submit(): void
    {
        if (!$this->tanggal_checkout_baru) {
            $this->messageText = __('booking.extend_select_date_first');
            return;
        }

        $this->validate([
            'metode_bayar' => 'required|string',
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        ]);

        $path = $this->bukti->store('bukti', 'public');
        $path = str_replace('\\', '/', $path);

        $extendStart = $this->pemesanan->tanggal_checkout->copy();
        $extendEnd = \Carbon\Carbon::parse($this->tanggal_checkout_baru);
        $days = max(1, $extendStart->diffInDays($extendEnd));
        $totalBayar = (float) ($this->pemesanan->kamar->harga * $days);

        // Create extend booking
        $extendBooking = Pemesanan::create([
            'wisatawan_id' => $this->pemesanan->wisatawan_id,
            'kamar_id' => $this->pemesanan->kamar_id,
            'tanggal_checkin' => $extendStart->format('Y-m-d'),
            'tanggal_checkout' => $extendEnd->format('Y-m-d'),
            'jumlah_hari' => $days,
            'total_bayar' => $totalBayar,
            'status' => Pemesanan::STATUS_PENDING,
            'is_extend' => true,
            'extend_from_id' => $this->pemesanan->id,
        ]);

        // Create payment
        Pembayaran::create([
            'pemesanan_id' => $extendBooking->id,
            'metode_bayar' => $this->metode_bayar,
            'jumlah' => $totalBayar,
            'bukti_pembayaran' => $path,
            'status' => Pembayaran::STATUS_PENDING,
        ]);

        // Mark original booking as extending
        $this->pemesanan->update(['status' => Pemesanan::STATUS_EXTEND]);

        $this->submitted = true;
        $this->extendBookingId = $extendBooking->id;
        $this->redirectRoute('booking.show', $extendBooking->id);
    }

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

    public function renderDayCell($day): string
    {
        if ($day === null) {
            return '<div></div>';
        }

        $booked = in_array($day, $this->bookedDates);
        $maintenance = in_array($day, $this->maintenanceDates);
        $selectedDate = \Carbon\Carbon::create($this->currentYear, $this->currentMonth, $day);

        $extendStart = $this->pemesanan->tanggal_checkout->copy()->startOfDay();
        $isPast = $selectedDate->lt($extendStart);
        $isCurrentCheckout = $selectedDate->equalTo($extendStart);

        $isSelected = $this->tanggal_checkout_baru &&
            $selectedDate->format('Y-m-d') === $this->tanggal_checkout_baru;

        $isInRange = false;
        if ($this->tanggal_checkout_baru) {
            $newCheckout = \Carbon\Carbon::parse($this->tanggal_checkout_baru)->startOfDay();
            $isInRange = $selectedDate->gt($extendStart) && $selectedDate->lt($newCheckout);
        }

        $disabled = $isPast || $booked || $maintenance || $isCurrentCheckout;

        $buttonClass = "p-2.5 rounded-lg text-sm font-semibold transition-all duration-200 min-h-[80px] flex flex-col items-center justify-center shadow-sm break-words ";
        if ($isCurrentCheckout) {
            $buttonClass .= "bg-sky-100 text-sky-700 cursor-not-allowed border-2 border-sky-300";
        } elseif ($maintenance && !$isPast) {
            $buttonClass .= "bg-amber-50 text-amber-500 cursor-not-allowed border-2 border-amber-200";
        } elseif ($isPast || $booked) {
            $buttonClass .= "bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200";
        } elseif ($isSelected) {
            $buttonClass .= "bg-gradient-to-br from-violet-500 to-violet-600 text-white shadow-md hover:shadow-lg transform hover:scale-105 border-2 border-violet-400";
        } elseif ($isInRange) {
            $buttonClass .= "bg-violet-50 text-violet-800 border-2 border-violet-200 hover:bg-violet-100";
        } else {
            $buttonClass .= "bg-white border-2 border-sky-200 text-slate-800 hover:bg-sky-50 hover:border-sky-400 hover:shadow-md transform hover:-translate-y-0.5";
        }

        $showPrice = (!$disabled && !$isSelected && !$isInRange) || $isSelected;
        $priceColor = $isSelected ? 'text-white text-opacity-90' : 'text-sky-600 font-medium';
        $price = number_format($this->pemesanan->kamar->harga, 0, ',', '.');

        $html = '<button type="button" wire:click="selectDate(' . $day . ')"';
        if ($disabled) $html .= ' disabled';
        $html .= ' class="' . $buttonClass . '">';
        $html .= '<div class="text-lg font-bold mb-1">' . $day . '</div>';

        if ($isCurrentCheckout) {
            $parts = explode('|', __('booking.extend_calendar_checkout_label'));
            $html .= '<div class="text-[10px] text-sky-600 font-semibold leading-tight text-center">' . ($parts[0] ?? '') . '<br>' . ($parts[1] ?? '') . '</div>';
        } elseif ($showPrice) {
            $html .= '<div class="text-[10px] leading-tight ' . $priceColor . ' text-center">IDR<br>' . $price . '</div>';
        }
        if ($maintenance && !$isPast && !$isCurrentCheckout) {
            $mParts = explode('|', __('booking.extend_calendar_maintenance_label'));
            $html .= '<div class="text-[10px] text-amber-500 mt-1 font-semibold leading-tight text-center">' . ($mParts[0] ?? '') . '<br>' . ($mParts[1] ?? '') . '</div>';
        } elseif (($booked) && !$isCurrentCheckout) {
            $naParts = explode('|', __('booking.extend_calendar_not_available_label'));
            $html .= '<div class="text-xs text-gray-400 mt-1 font-semibold">' . ($naParts[0] ?? '') . '<br>' . ($naParts[1] ?? '') . '</div>';
        }
        $html .= '</button>';

        return $html;
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">
                    <svg class="inline-block w-7 h-7 mr-2 text-violet-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('booking.extend_page_title') }}
                </h1>
                <p class="mt-1 text-sm text-slate-600">{!! __('booking.extend_page_subtitle', ['room' => '<strong>' . $pemesanan->kamar->nama_kamar . '</strong>']) !!}</p>
            </div>
            <a href="{{ route('booking.show', $pemesanan->id) }}" class="inline-flex items-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">{{ __('booking.extend_back') }}</a>
        </div>

        {{-- Current booking info --}}
        <div class="rounded-2xl border border-violet-100 bg-violet-50/30 p-5 mb-6">
            <h3 class="text-base font-semibold text-violet-900">{{ __('booking.extend_current_booking') }}</h3>
            <div class="mt-3 grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                <div class="rounded-lg border border-violet-100 bg-white p-3">
                    <div class="text-slate-500">{{ __('booking.extend_room') }}</div>
                    <div class="font-semibold text-slate-900">{{ $pemesanan->kamar->nama_kamar }}</div>
                </div>
                <div class="rounded-lg border border-violet-100 bg-white p-3">
                    <div class="text-slate-500">{{ __('booking.extend_checkin') }}</div>
                    <div class="font-semibold text-slate-900">{{ $pemesanan->tanggal_checkin->format('d M Y') }}</div>
                </div>
                <div class="rounded-lg border border-violet-100 bg-white p-3">
                    <div class="text-slate-500">{{ __('booking.extend_checkout') }}</div>
                    <div class="font-semibold text-slate-900">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</div>
                    <div class="text-xs text-slate-500">11:00 AM</div>
                </div>
                <div class="rounded-lg border border-violet-100 bg-white p-3">
                    <div class="text-slate-500">{{ __('booking.extend_price_per_night') }}</div>
                    <div class="font-semibold text-slate-900">Rp {{ number_format($pemesanan->kamar->harga, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calendar --}}
            <div class="lg:col-span-2">
                <div class="rounded-2xl border-2 border-violet-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">{{ __('booking.extend_select_date_title') }}</h3>
                    <p class="text-sm text-slate-600 mb-4">{!! __('booking.extend_select_date_subtitle', ['date' => '<strong>' . $pemesanan->tanggal_checkout->format('d M Y') . '</strong>']) !!}</p>

                    {{-- Month Navigation --}}
                    <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-violet-100">
                        <button type="button" wire:click="previousMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-violet-600 hover:bg-violet-100 hover:text-violet-700 transition-all duration-200 shadow-sm hover:shadow">‹</button>
                        <h3 class="text-xl font-bold text-slate-800">{{ ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$currentMonth - 1] }} {{ $currentYear }}</h3>
                        <button type="button" wire:click="nextMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-violet-600 hover:bg-violet-100 hover:text-violet-700 transition-all duration-200 shadow-sm hover:shadow">›</button>
                    </div>

                    {{-- Calendar Grid --}}
                    <div class="grid grid-cols-7 gap-2 mb-6">
                        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $dayName)
                            <div class="text-center font-bold text-slate-600 py-2 text-xs uppercase tracking-wide">{{ $dayName }}</div>
                        @endforeach

                        @foreach($this->getCalendarDays() as $day)
                            {!! $this->renderDayCell($day) !!}
                        @endforeach
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap gap-4 text-xs text-slate-600 border-t pt-4">
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-sky-100 border-2 border-sky-300"></div> {{ __('booking.extend_legend_current_checkout') }}</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-violet-500 border-2 border-violet-400"></div> {{ __('booking.extend_legend_extend_checkout') }}</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-violet-50 border-2 border-violet-200"></div> {{ __('booking.extend_legend_extend_period') }}</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-gray-100 border border-gray-200"></div> {{ __('booking.extend_legend_unavailable') }}</div>
                        <div class="flex items-center gap-2"><div class="w-4 h-4 rounded bg-amber-50 border-2 border-amber-200"></div> {{ __('booking.extend_legend_maintenance') }}</div>
                    </div>

                    @if($messageText)
                        <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 p-4 text-sm">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                <span>{{ $messageText }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Payment sidebar --}}
            <div>
                @if($tanggal_checkout_baru)
                    <div class="rounded-2xl border border-violet-100 bg-white p-5 shadow-sm">
                        <h3 class="text-lg font-semibold text-violet-900">{{ __('booking.extend_summary_title') }}</h3>

                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600">{{ __('booking.extend_old_checkout') }}</span>
                                <span class="font-medium">{{ $pemesanan->tanggal_checkout->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">{{ __('booking.extend_new_checkout') }}</span>
                                <span class="font-semibold text-violet-700">{{ \Carbon\Carbon::parse($tanggal_checkout_baru)->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">{{ __('booking.extend_extra_nights') }}</span>
                                <span class="font-medium">{{ $jumlah_hari_extend }} {{ trans_choice('booking.extend_nights_unit', $jumlah_hari_extend) }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between">
                                <span class="text-slate-900 font-semibold">{{ __('booking.extend_total') }}</span>
                                <span class="text-lg font-bold text-violet-700">Rp {{ number_format($total_extend, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <form wire:submit="submit" class="mt-5 space-y-4 text-sm border-t pt-4">
                            <h4 class="font-semibold text-slate-900">{{ __('booking.extend_payment_title') }}</h4>

                            <div>
                                <label class="ui-label">{{ __('booking.extend_payment_method') }}</label>
                                <select wire:model="metode_bayar" class="ui-select">
                                    <option value="transfer">{{ __('booking.extend_transfer') }}</option>
                                    <option value="tunai">{{ __('booking.extend_cash') }}</option>
                                </select>
                            </div>

                            @if($metode_bayar === 'transfer')
                                <div class="rounded-lg border border-violet-100 bg-violet-50 p-3">
                                    <div class="text-sm font-medium text-violet-900">{{ __('booking.extend_destination_account') }}</div>
                                    <div class="mt-2 grid gap-2 text-sm">
                                        @forelse($banks as $b)
                                            <div class="rounded-md bg-white border border-violet-100 px-3 py-2">
                                                <div class="text-violet-700">{{ $b['nama_bank'] }}</div>
                                                <div class="font-mono text-slate-900">{{ $b['no_transfer'] }}</div>
                                            </div>
                                        @empty
                                            <div class="text-slate-600">{{ __('booking.extend_no_bank') }}</div>
                                        @endforelse
                                    </div>
                                </div>
                            @endif

                            <input type="hidden" wire:model="jumlah" />

                            <div>
                                <label class="ui-label">{{ __('booking.extend_payment_proof') }}</label>
                                <input type="file" wire:model="bukti" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" />
                                @error('bukti') <div class="ui-error">{{ $message }}</div> @enderror
                            </div>

                            <div class="flex gap-3">
                                <button class="flex-1 inline-flex items-center justify-center rounded-lg bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-violet-500 shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ __('booking.extend_confirm') }}
                                </button>
                                <button type="button" wire:click="resetDates" class="flex-1 inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                    {{ __('booking.extend_reset') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                        <p class="mt-3 text-sm text-slate-600">{{ __('booking.extend_placeholder_message') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
