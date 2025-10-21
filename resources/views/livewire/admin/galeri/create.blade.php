<?php

use App\Models\Galeri;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?string $title = null;
    public string $status = 'active';
    public int $urutan = 0;
    public $file = null;
    public ?string $url = null;

    public function save(): void
    {
        $data = $this->validate([
            'title' => 'nullable|string|max:100',
            'status' => 'required|string',
            'urutan' => 'required|integer|min:0',
            'url' => 'nullable|url',
            // Naikkan batas ukuran ke 10MB (10240 KB)
            'file' => 'nullable|file|image|max:10240',
        ]);

        $path = $this->file ? $this->file->store('galeri', 'public') : ($this->url ?? '');
        if ($path && !$this->url) {
            $path = ltrim(str_replace('\\', '/', $path), '/');
        }

        Galeri::create([
            'title' => $this->title,
            'status' => $this->status,
            'urutan' => $this->urutan,
            'path' => $path,
        ]);

        $this->redirectRoute('admin.galeri.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Tambah Gambar Galeri</h1>
        <p class="mt-1 text-slate-600 text-sm">Unggah gambar atau gunakan URL eksternal.</p>
    </div>

    <form wire:submit="save" class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 ui-card">
        <div>
            <label class="ui-label">Judul</label>
            <input type="text" wire:model="title" class="ui-input" />
        </div>
        <div>
            <label class="ui-label">Status</label>
            <select wire:model="status" class="ui-select">
                <option value="active">active</option>
                <option value="hidden">hidden</option>
            </select>
        </div>
        <div>
            <label class="ui-label">Urutan</label>
            <input type="number" wire:model="urutan" class="ui-input" />
        </div>
        <div>
            <label class="ui-label">Upload Gambar</label>
            <input type="file" wire:model="file" accept="image/*" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">atau URL Gambar</label>
            <input type="text" wire:model="url" placeholder="https://..." class="ui-input" />
        </div>
        <div class="sm:col-span-2 flex items-center gap-3">
            <button class="ui-btn-primary">Simpan</button>
            <a href="{{ route('admin.galeri.index') }}" class="text-sm text-slate-700 hover:text-slate-900">Batal</a>
        </div>
    </form>
</section>
