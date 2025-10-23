<?php

use App\Models\Kamar;
use App\Models\Fasilitas;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\KamarFoto;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageUploader;

new class extends Component {
    use WithFileUploads;
    public Kamar $kamar;

    public string $nama_kamar = '';
    public ?string $tipe_kamar = null;
    public float $harga = 0.0;
    public ?string $deskripsi = null;
    public string $status = 'available';
    public array $images = [];
    public array $fasilitasList = [];
    public array $fasilitas_ids = [];

    public function mount(Kamar $kamar): void
    {
        $this->kamar = $kamar;
        $this->nama_kamar = $kamar->nama_kamar;
        $this->tipe_kamar = $kamar->tipe_kamar;
        $this->harga = (float) $kamar->harga;
        $this->deskripsi = $kamar->deskripsi;
        $this->status = $kamar->status;
        $this->fasilitasList = Fasilitas::orderBy('nama')->get()->toArray();
        $this->fasilitas_ids = $kamar->fasilitas()->pluck('fasilitas.id')->toArray();
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama_kamar' => 'required|string|max:100',
            'tipe_kamar' => 'nullable|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'status' => 'required|string',
            'images' => 'array|max:10',
            'images.*' => 'image|max:25600',
            'fasilitas_ids' => 'array',
            'fasilitas_ids.*' => 'integer|exists:fasilitas,id',
        ]);

        $images = $data['images'] ?? [];
        unset($data['images'], $data['fasilitas_ids']);

        $this->kamar->update($data);
        $this->kamar->fasilitas()->sync($this->fasilitas_ids ?? []);

        if (!empty($images)) {
            $orderStart = (int) ($this->kamar->fotos()->max('urutan') ?? 0);
            foreach ($images as $idx => $file) {
                try {
                    $path = ImageUploader::storeCompressed($file, 'kamar', 2048, 1920, 1920);
                } catch (\Throwable $e) {
                    $path = $file->store('kamar', 'public');
                    $path = $path ? ltrim(str_replace('\\\\', '/', $path), '/') : null;
                }
                if ($path) {
                    KamarFoto::create([
                        'kamar_id' => $this->kamar->id,
                        'path' => $path,
                        'urutan' => $orderStart + $idx + 1,
                    ]);
                }
            }
            $this->images = [];
        }
        $this->redirectRoute('admin.kamar.index');
    }

    public function deleteFoto(int $fotoId): void
    {
        $foto = $this->kamar->fotos()->whereKey($fotoId)->first();
        if ($foto) {
            Storage::disk('public')->delete($foto->path);
            $foto->delete();
        }
        $this->kamar->refresh();
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
        <h1 class="text-2xl font-semibold text-slate-900">Edit Kamar</h1>
        <p class="mt-1 text-slate-600 text-sm">Perbarui informasi kamar yang dipilih.</p>
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
            <label class="ui-label">Fasilitas</label>
            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($fasilitasList as $f)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" value="{{ $f['id'] }}" wire:model="fasilitas_ids" class="ui-checkbox">
                    <span>{{ $f['nama'] }}</span>
                </label>
                @endforeach
            </div>
            @error('fasilitas_ids') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Foto Kamar (tambahkan atau hapus)</label>
            <input type="file" multiple wire:model="images" accept="image/*" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
            @error('images') <div class="ui-error">{{ $message }}</div> @enderror
            @error('images.*') <div class="ui-error">{{ $message }}</div> @enderror

            <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                @forelse($kamar->fotos as $f)
                    <div class="relative group">
                        <div class="aspect-[4/3] overflow-hidden rounded border bg-slate-100">
                            <img src="{{ asset('storage/'.ltrim($f->path,'/')) }}" class="h-full w-full object-cover" />
                        </div>
                        <button type="button" wire:click="deleteFoto({{ $f->id }})" class="absolute top-1 right-1 hidden group-hover:inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 bg-white/90 hover:bg-rose-50">Hapus</button>
                    </div>
                @empty
                    <div class="text-slate-500 text-sm">Belum ada foto.</div>
                @endforelse

                @if($images)
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
                @endif
            </div>
        </div>
        <div class="sm:col-span-2 flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="{{ route('admin.kamar.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>
