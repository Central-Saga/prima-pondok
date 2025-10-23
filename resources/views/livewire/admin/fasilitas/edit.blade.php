<?php

use App\Models\Fasilitas;
use Livewire\Volt\Component;

new class extends Component {
    public Fasilitas $fasilitas;

    public string $nama = '';

    public function mount(Fasilitas $fasilitas): void
    {
        $this->fasilitas = $fasilitas;
        $this->nama = $fasilitas->nama;
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama' => 'required|string|max:150',
        ]);
        $this->fasilitas->update($data);
        $this->redirectRoute('admin.fasilitas.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit Fasilitas</h1>
        <p class="mt-1 ui-help">Perbarui nama fasilitas.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div>
            <label class="ui-label">Nama Fasilitas</label>
            <input type="text" wire:model="nama" class="ui-input" />
            @error('nama') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="{{ route('admin.fasilitas.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>

