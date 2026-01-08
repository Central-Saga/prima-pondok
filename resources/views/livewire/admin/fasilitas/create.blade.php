<?php

use App\Models\Fasilitas;
use Livewire\Volt\Component;

new class extends Component {
    public string $nama = '';
    public string $nama_en = '';

    public function save(): void
    {
        $data = $this->validate([
            'nama' => 'required|string|max:150',
            'nama_en' => 'nullable|string|max:150',
        ]);
        Fasilitas::create($data);
        $this->redirectRoute('admin.fasilitas.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Tambah Fasilitas</h1>
        <p class="mt-1 ui-help">Masukkan nama fasilitas.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div>
            <label class="ui-label">Nama Fasilitas</label>
            <input type="text" wire:model="nama" class="ui-input" />
            @error('nama') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Nama (English)</label>
            <input type="text" wire:model="nama_en" class="ui-input" />
            @error('nama_en') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Simpan</button>
            <a href="{{ route('admin.fasilitas.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>
