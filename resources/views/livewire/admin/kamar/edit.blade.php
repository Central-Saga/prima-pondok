<?php

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\KamarMaintenance;
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
    public ?string $deskripsi_en = null;
    public string $status = 'available';
    public array $images = [];
    public array $fasilitasList = [];
    public array $fasilitas_ids = [];

    // Maintenance scheduling
    public bool $showMaintenanceModal = false;
    public ?string $mt_tanggal_mulai = null;
    public ?string $mt_tanggal_selesai = null;
    public ?string $mt_keterangan = null;
    public ?int $editingMaintenanceId = null;

    public function mount(Kamar $kamar): void
    {
        $this->kamar = $kamar;
        $this->nama_kamar = $kamar->nama_kamar;
        $this->tipe_kamar = $kamar->tipe_kamar;
        $this->harga = (float) $kamar->harga;
        $this->deskripsi = (string) $kamar->getRawOriginal('deskripsi');
        $this->deskripsi_en = (string) ($kamar->getRawOriginal('deskripsi_en') ?? '');
        $this->status = $kamar->status;
        $this->fasilitasList = Fasilitas::orderBy('nama')->get()->toArray();
        $this->fasilitas_ids = $kamar->fasilitas()->pluck('fasilitas.id')->toArray();
    }

    public function updatedStatus($value): void
    {
        if ($value === 'maintenance') {
            $this->showMaintenanceModal = true;
        }
    }

    public function openMaintenanceModal(): void
    {
        $this->showMaintenanceModal = true;
    }

    public function closeMaintenanceModal(): void
    {
        $this->showMaintenanceModal = false;
        $this->resetMaintenanceForm();
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama_kamar' => 'required|string|max:100',
            'tipe_kamar' => 'nullable|string|max:100',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string',
            'deskripsi_en' => 'nullable|string',
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

    // === Maintenance Schedule Methods ===

    public function getMaintenancesProperty()
    {
        return $this->kamar->maintenances()->orderBy('tanggal_mulai')->get();
    }

    public function saveMaintenance(): void
    {
        $this->validate([
            'mt_tanggal_mulai' => 'required|date',
            'mt_tanggal_selesai' => 'required|date|after_or_equal:mt_tanggal_mulai',
            'mt_keterangan' => 'nullable|string|max:500',
        ], [
            'mt_tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'mt_tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'mt_tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        // Cek overlap dengan jadwal maintenance lain
        $overlap = $this->kamar->maintenances()
            ->where('tanggal_mulai', '<=', $this->mt_tanggal_selesai)
            ->where('tanggal_selesai', '>=', $this->mt_tanggal_mulai)
            ->when($this->editingMaintenanceId, fn($q) => $q->where('id', '!=', $this->editingMaintenanceId))
            ->exists();

        if ($overlap) {
            $this->addError('mt_tanggal_mulai', 'Jadwal maintenance tumpang tindih dengan jadwal lain.');
            return;
        }

        if ($this->editingMaintenanceId) {
            $maintenance = $this->kamar->maintenances()->whereKey($this->editingMaintenanceId)->first();
            if ($maintenance) {
                $maintenance->update([
                    'tanggal_mulai' => $this->mt_tanggal_mulai,
                    'tanggal_selesai' => $this->mt_tanggal_selesai,
                    'keterangan' => $this->mt_keterangan,
                ]);
            }
        } else {
            $this->kamar->maintenances()->create([
                'tanggal_mulai' => $this->mt_tanggal_mulai,
                'tanggal_selesai' => $this->mt_tanggal_selesai,
                'keterangan' => $this->mt_keterangan,
            ]);
        }

        $this->resetMaintenanceForm();
        $this->kamar->refresh();
    }

    public function editMaintenance(int $id): void
    {
        $m = $this->kamar->maintenances()->whereKey($id)->first();
        if ($m) {
            $this->editingMaintenanceId = $m->id;
            $this->mt_tanggal_mulai = $m->tanggal_mulai->format('Y-m-d');
            $this->mt_tanggal_selesai = $m->tanggal_selesai->format('Y-m-d');
            $this->mt_keterangan = $m->keterangan;
        }
    }

    public function deleteMaintenance(int $id): void
    {
        $this->kamar->maintenances()->whereKey($id)->delete();
        $this->kamar->refresh();
        if ($this->editingMaintenanceId === $id) {
            $this->resetMaintenanceForm();
        }
    }

    public function resetMaintenanceForm(): void
    {
        $this->editingMaintenanceId = null;
        $this->mt_tanggal_mulai = null;
        $this->mt_tanggal_selesai = null;
        $this->mt_keterangan = null;
        $this->resetErrorBag(['mt_tanggal_mulai', 'mt_tanggal_selesai', 'mt_keterangan']);
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
            <select wire:model.live="status" class="ui-select">
                <option value="available">available</option>
                <option value="maintenance">maintenance</option>
            </select>
            @if($status === 'maintenance')
                <button type="button" wire:click="openMaintenanceModal" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-amber-700 hover:text-amber-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    Atur Jadwal Maintenance ({{ $this->maintenances->count() }})
                </button>
            @endif
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Deskripsi Kamar</label>
            <textarea wire:model="deskripsi" rows="3" class="ui-textarea"></textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Deskripsi Kamar (English)</label>
            <textarea wire:model="deskripsi_en" rows="3" class="ui-textarea"></textarea>
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

    {{-- Maintenance Schedule Modal --}}
    @if($showMaintenanceModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <button type="button" wire:click="closeMaintenanceModal" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
        <div role="dialog" aria-modal="true" class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Jadwal Maintenance</h2>
                    <p class="text-sm text-slate-600">Atur jadwal maintenance per tanggal. Tanggal yang di-maintenance tidak bisa dipesan oleh wisatawan.</p>
                </div>
                <button type="button" wire:click="closeMaintenanceModal" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            {{-- Form Tambah / Edit Maintenance --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-lg border border-sky-100 bg-sky-50/50">
                <div>
                    <label class="ui-label">Tanggal Mulai</label>
                    <input type="date" wire:model="mt_tanggal_mulai" class="ui-input" />
                    @error('mt_tanggal_mulai') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ui-label">Tanggal Selesai</label>
                    <input type="date" wire:model="mt_tanggal_selesai" class="ui-input" />
                    @error('mt_tanggal_selesai') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="ui-label">Keterangan Maintenance <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="mt_keterangan" rows="2" class="ui-textarea" placeholder="Contoh: Perbaikan AC, Renovasi kamar mandi, dll."></textarea>
                    @error('mt_keterangan') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
                <div class="sm:col-span-2 flex items-center gap-3">
                    <button type="button" wire:click="saveMaintenance" class="ui-btn-primary">
                        {{ $editingMaintenanceId ? 'Update Jadwal' : 'Tambah Jadwal' }}
                    </button>
                    @if($editingMaintenanceId)
                        <button type="button" wire:click="resetMaintenanceForm" class="ui-btn-secondary">Batal Edit</button>
                    @endif
                </div>
            </div>

            {{-- Daftar Jadwal Maintenance --}}
            @if($this->maintenances->isNotEmpty())
            <div class="mt-4 overflow-hidden rounded-xl border border-sky-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="py-2 px-3 text-left">Tanggal Mulai</th>
                            <th class="py-2 px-3 text-left">Tanggal Selesai</th>
                            <th class="py-2 px-3 text-left">Keterangan</th>
                            <th class="py-2 px-3 text-left">Status</th>
                            <th class="py-2 px-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($this->maintenances as $mt)
                        <tr class="{{ $editingMaintenanceId === $mt->id ? 'bg-sky-50' : '' }}">
                            <td class="py-2 px-3">{{ $mt->tanggal_mulai->format('d M Y') }}</td>
                            <td class="py-2 px-3">{{ $mt->tanggal_selesai->format('d M Y') }}</td>
                            <td class="py-2 px-3 text-slate-600">{{ $mt->keterangan ?: '-' }}</td>
                            <td class="py-2 px-3">
                                @if($mt->tanggal_selesai->isPast())
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200">Selesai</span>
                                @elseif($mt->tanggal_mulai->isFuture())
                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200">Terjadwal</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Sedang Berlangsung</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right">
                                <button type="button" wire:click="editMaintenance({{ $mt->id }})" class="ui-btn-secondary">Edit</button>
                                <button type="button" onclick="if(!confirm('Hapus jadwal maintenance ini?')){event.stopImmediatePropagation()}" wire:click="deleteMaintenance({{ $mt->id }})" class="ml-1 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="mt-4 text-sm text-slate-500">Belum ada jadwal maintenance untuk kamar ini.</p>
            @endif

            <div class="mt-5 flex justify-end">
                <button type="button" wire:click="closeMaintenanceModal" class="ui-btn-secondary">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</section>
