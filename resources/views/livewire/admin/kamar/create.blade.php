<?php

use App\Models\Kamar;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\KamarFoto;
use App\Support\ImageUploader;

new class extends Component {
    use WithFileUploads;
    public string $nama_kamar = '';
    public ?string $tipe_kamar = null;
    public float $harga = 0.0;
    public ?string $deskripsi = null;
    public string $status = 'available';
    public array $images = [];
    public array $newImages = [];

    public function save(): void
    {
        $data = $this->validate([
            'nama_kamar' => 'required|string|max:100',
            'tipe_kamar' => 'nullable|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string',
            'images' => 'array|max:10',
            // Naikkan batas ukuran per file ke 25MB (25600 KB)
            'images.*' => 'image|max:25600',
        ]);

        $images = $data['images'] ?? [];
        unset($data['images']);

        $kamar = Kamar::create($data);

        if (!empty($images)) {
            $order = 0;
            foreach ($images as $file) {
                try {
                    $path = ImageUploader::storeCompressed($file, 'kamar', 2048, 1920, 1920);
                } catch (\Throwable $e) {
                    $path = $file->store('kamar', 'public');
                    $path = $path ? ltrim(str_replace('\\', '/', $path), '/') : null;
                }
                if ($path) {
                    KamarFoto::create([
                        'kamar_id' => $kamar->id,
                        'path' => $path,
                        'urutan' => $order++,
                    ]);
                }
            }
        }
        $this->redirectRoute('admin.kamar.index');
    }

    public function updatedNewImages(): void
    {
        if (!empty($this->newImages)) {
            foreach ($this->newImages as $file) {
                $this->images[] = $file;
            }
            $this->newImages = [];
        }
    }

    public function removeImage(int $index): void
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images);
        }
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Tambah Kamar</h1>
        <p class="mt-1 text-slate-600 text-sm">Lengkapi detail kamar dan harga per malam.</p>
    </div>

    <form wire:submit="save" class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 ui-card">
        <div>
            <label class="ui-label">Nama Kamar</label>
            <input type="text" wire:model="nama_kamar" class="ui-input" />
            @error('nama_kamar') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Tipe</label>
            <input type="text" wire:model="tipe_kamar" class="ui-input" />
        </div>
        <div>
            <label class="ui-label">Harga per malam</label>
            <input type="number" step="1" wire:model="harga" class="ui-input" />
            @error('harga') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Status</label>
            <select wire:model="status" class="ui-select">
                <option value="available">available</option>
                <option value="maintenance">maintenance</option>
                <option value="unavailable">unavailable</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Deskripsi Kamar</label>
            <textarea wire:model="deskripsi" rows="3" class="ui-textarea"></textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Foto Kamar (boleh lebih dari satu)</label>
            <input type="file" multiple wire:model="newImages" accept="image/*" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
            @error('images') <div class="ui-error">{{ $message }}</div> @enderror
            @error('images.*') <div class="ui-error">{{ $message }}</div> @enderror
            @if($images)
                <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach($images as $i => $preview)
                        <div class="relative group">
                            <div class="aspect-[4/3] bg-slate-100 rounded overflow-hidden flex items-center justify-center text-slate-400">
                                @if(method_exists($preview,'temporaryUrl'))
                                    <img src="{{ $preview->temporaryUrl() }}" class="h-full w-full object-cover" />
                                @else
                                    <span>Preview</span>
                                @endif
                            </div>
                            <button type="button" wire:click="removeImage({{ $i }})" class="absolute top-1 right-1 hidden group-hover:inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 bg-white/90 hover:bg-rose-50">Hapus</button>
                            <span class="absolute bottom-1 left-1 inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-medium text-sky-700 ring-1 ring-inset ring-sky-200 bg-white/90">Baru</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="sm:col-span-2 flex items-center gap-3">
            <button class="ui-btn-primary">Simpan</button>
            <a href="{{ route('admin.kamar.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>
