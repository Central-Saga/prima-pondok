<?php

use App\Models\Setting;
use Livewire\Volt\Component;

new class extends Component {
    public string $hero_title = '';
    public string $hero_subtitle = '';
    public string $contact_phone = '';
    public string $contact_email = '';
    public string $contact_address = '';

    public function mount(): void
    {
        $this->hero_title = Setting::get('hero_title', 'Home Stay Pondok Teges');
        $this->hero_subtitle = Setting::get('hero_subtitle', 'Rasakan kenyamanan menginap di Ubud.');
        $this->contact_phone = Setting::get('contact_phone', '+62-812-0000-0000');
        $this->contact_email = Setting::get('contact_email', 'info@pondokteges.local');
        $this->contact_address = Setting::get('contact_address', 'Ubud, Bali — Indonesia');
    }

    private function saveValue($key, $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public function save(): void
    {
        $this->validate([
            'hero_title' => 'required|string|max:200',
            'hero_subtitle' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:50',
            'contact_email' => 'required|email|max:100',
            'contact_address' => 'required|string|max:255',
        ]);

        $this->saveValue('hero_title', $this->hero_title);
        $this->saveValue('hero_subtitle', $this->hero_subtitle);
        $this->saveValue('contact_phone', $this->contact_phone);
        $this->saveValue('contact_email', $this->contact_email);
        $this->saveValue('contact_address', $this->contact_address);

        session()->flash('saved', 'Konten landing disimpan.');
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Konten Landing</h1>
            <p class="mt-1 ui-help">Atur teks hero dan informasi kontak yang muncul di halaman utama.</p>
        </div>
    </div>

    @if (session('saved'))
        <div class="mt-4 rounded-lg bg-emerald-50 text-emerald-700 p-3 text-sm">{{ session('saved') }}</div>
    @endif

    <form wire:submit="save" class="mt-6 ui-card grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="ui-label">Hero Title</label>
            <input type="text" wire:model="hero_title" class="ui-input" />
            @error('hero_title') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Hero Subtitle</label>
            <input type="text" wire:model="hero_subtitle" class="ui-input" />
            @error('hero_subtitle') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Telepon/WA</label>
            <input type="text" wire:model="contact_phone" class="ui-input" />
            @error('contact_phone') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Email</label>
            <input type="email" wire:model="contact_email" class="ui-input" />
            @error('contact_email') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Alamat</label>
            <input type="text" wire:model="contact_address" class="ui-input" />
            @error('contact_address') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="sm:col-span-2">
            <button class="ui-btn-primary">Simpan</button>
        </div>
    </form>
</section>

