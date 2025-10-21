<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
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

    public function mount(Kamar $kamar): void
    {
        $this->kamar = $kamar->load('fotos');
        $this->activeIndex = 0;
    }

    public function updated($field): void
    {
        if (in_array($field, ['tanggal_checkin','tanggal_checkout'])) {
            $this->recalc();
        }
    }

    private function recalc(): void
    {
        $this->messageText = '';
        $this->jumlah_hari = 1;
        $this->total_bayar = 0;
        if (! $this->tanggal_checkin || ! $this->tanggal_checkout) {
            return;
        }

        $start = \Carbon\Carbon::parse($this->tanggal_checkin)->startOfDay();
        $end = \Carbon\Carbon::parse($this->tanggal_checkout)->startOfDay();
        if ($end->lte($start)) {
            $this->messageText = 'Tanggal checkout harus setelah checkin';
            return;
        }

        $days = $start->diffInDays($end);
        $this->jumlah_hari = max(1, $days);
        $this->total_bayar = (float) ($this->kamar->harga * $this->jumlah_hari);

        $overlap = Pemesanan::where('kamar_id', $this->kamar->id)
            ->whereIn('status', [Pemesanan::STATUS_PENDING, Pemesanan::STATUS_CONFIRMED])
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($x) use ($start, $end) {
                    $x->where('tanggal_checkin', '<', $end)
                      ->where('tanggal_checkout', '>', $start);
                });
            })->exists();

        if ($overlap) {
            $this->messageText = 'Kamar tidak tersedia pada tanggal tersebut';
        }
    }

    public function pesan(): void
    {
        $this->validate([
            'tanggal_checkin' => 'required|date',
            'tanggal_checkout' => 'required|date|after:tanggal_checkin',
        ]);

        $this->recalc();
        if ($this->messageText) {
            $this->addError('tanggal_checkin', $this->messageText);
            return;
        }

        $user = Auth::user();
        if (! $user) {
            session()->put('url.intended', url()->current());
            $this->redirectRoute('login');
            return;
        }

        // ensure wisatawan profile
        if (! $user->hasRole('wisatawan')) {
            try { $user->assignRole('wisatawan'); } catch (\Throwable $e) {}
        }
        if (! $user->wisatawan) {
            $user->wisatawan()->create([
                'name' => $user->name,
                'status' => 'active',
            ]);
        }

        $p = Pemesanan::create([
            'wisatawan_id' => $user->wisatawan->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_checkin' => $this->tanggal_checkin,
            'tanggal_checkout' => $this->tanggal_checkout,
            'jumlah_hari' => $this->jumlah_hari,
            'total_bayar' => $this->total_bayar,
            'status' => Pemesanan::STATUS_PENDING,
        ]);

        $this->redirectRoute('booking.show', $p->id);
    }

    public function select(int $index): void
    {
        $count = $this->kamar->fotos?->count() ?? 0;
        if ($index >= 0 && $index < $count) {
            $this->activeIndex = $index;
        }
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8">
            <div>
                <div class="aspect-[16/9] lg:aspect-[21/9] rounded-xl overflow-hidden border bg-slate-100">
                    @php($fotoCount = ($kamar->fotos ?? collect())->count())
                    @if($fotoCount > 0)
                        @php($main = $kamar->fotos[$activeIndex] ?? $kamar->fotos->first())
                        <img src="{{ url('media/'.$main->path) }}" alt="{{ $kamar->nama_kamar }}" class="h-full w-full object-cover" />
                    @else
                        <div class="h-full w-full flex items-center justify-center text-slate-400">Tidak ada foto</div>
                    @endif
                </div>
                @if(($kamar->fotos ?? collect())->isNotEmpty())
                    <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        @foreach($kamar->fotos as $i => $f)
                            <button type="button" wire:click="select({{ $i }})" class="group aspect-[4/3] rounded-lg overflow-hidden border bg-slate-100 ring-2 transition focus:outline-none {{ $activeIndex === $i ? 'ring-sky-400' : 'ring-transparent hover:ring-slate-300' }}">
                                <img src="{{ url('media/'.$f->path) }}" alt="Foto {{ $i+1 }}" class="h-full w-full object-cover" />
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ $kamar->nama_kamar }}</h1>
                    <div class="mt-2 text-slate-600">Tipe: <span class="font-medium text-slate-900">{{ $kamar->tipe_kamar ?: 'Standar' }}</span></div>
                    <div class="mt-2 text-slate-600">Harga per malam: <span class="font-semibold text-slate-900">Rp {{ number_format($kamar->harga,0,',','.') }}</span></div>
                    @if($kamar->fasilitas)
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-slate-900">Fasilitas</h2>
                            <div class="mt-2 whitespace-pre-line text-slate-700 leading-relaxed">{{ $kamar->fasilitas }}</div>
                        </div>
                    @endif
                </div>
                <!-- Booking card placed below facilities with some space -->
                <div id="booking" class="mt-8 sm:mt-10 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
                    <h3 class="text-lg font-semibold text-slate-900">Pesan Kamar Ini</h3>
                    <p class="mt-1 text-sm text-slate-600">Pilih tanggal check-in dan check-out.</p>

                    <form wire:submit="pesan" class="mt-4 space-y-4">
                        <div>
                            <label class="ui-label">Tanggal Check-in</label>
                            <input type="date" wire:model.live.debounce.1000ms="tanggal_checkin" class="ui-input" />
                            @error('tanggal_checkin') <div class="ui-error">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="ui-label">Tanggal Check-out</label>
                            <input type="date" wire:model.live.debounce.1000ms="tanggal_checkout" class="ui-input" />
                            @error('tanggal_checkout') <div class="ui-error">{{ $message }}</div> @enderror
                        </div>

                        @if($messageText)
                            <div class="rounded-md bg-rose-50 text-rose-700 p-3 text-sm">{{ $messageText }}</div>
                        @endif

                        <div class="rounded-lg border p-3 text-sm text-slate-700">
                            <div>Jumlah malam: <span class="font-semibold">{{ $jumlah_hari }}</span></div>
                            <div>Total bayar: <span class="font-semibold">Rp {{ number_format($total_bayar,0,',','.') }}</span></div>
                        </div>

                        <div class="pt-1 flex flex-col sm:flex-row sm:items-center sm:gap-3">
                            <button class="ui-btn-primary w-full sm:w-auto">Pesan Sekarang</button>
                            <a href="{{ route('home') }}" class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
