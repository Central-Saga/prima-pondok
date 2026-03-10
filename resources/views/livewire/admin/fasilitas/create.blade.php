<?php

use App\Models\Fasilitas;
use Livewire\Volt\Component;

new class extends Component {
    public string $nama = '';

    private function translateToEnglish(string $indonesian): string
    {
        $translations = [
            'Dapur' => 'kitchen',
            'Lemari / rak pakaian' => 'wardrobe',
            'Meja kecil / meja kerja dan kursi' => 'small table',
            'pendingin' => 'AC',
            'AC' => 'AC',
            'Teko Listrik' => 'electric kettle',
            'TV' => 'TV',
            'Wi-Fi' => 'Wi-Fi',
        ];

        return $translations[trim($indonesian)] ?? strtolower(trim($indonesian));
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama' => 'required|string|max:150',
        ]);
        
        $data['nama_en'] = $this->translateToEnglish($data['nama']);
        
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
            <label class="ui-label">Nama Fasilitas (Indonesia)</label>
            <input type="text" wire:model="nama" class="ui-input" placeholder="Contoh: Dapur, Wi-Fi, AC, TV" />
            @error('nama') <div class="ui-error">{{ $message }}</div> @enderror
            <p class="mt-1 text-xs text-slate-500">* Nama akan otomatis diterjemahkan ke bahasa Inggris</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Simpan</button>
            <a href="{{ route('admin.fasilitas.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>
