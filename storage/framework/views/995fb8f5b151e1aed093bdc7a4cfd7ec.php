<?php

use App\Models\Kamar;
use App\Models\Fasilitas;
use App\Models\KamarMaintenance;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\KamarFoto;
use Illuminate\Support\Facades\Storage;
use App\Support\ImageUploader;

?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit Kamar</h1>
        <p class="mt-1 text-slate-600 text-sm">Perbarui informasi kamar yang dipilih.</p>
    </div>

    <form wire:submit="save" class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 ui-card">
        <div>
            <label class="ui-label">Nama Kamar</label>
            <input type="text" wire:model="nama_kamar" class="ui-input" />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nama_kamar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div>
            <label class="ui-label">Tipe</label>
            <input type="text" wire:model="tipe_kamar" class="ui-input" />
        </div>
        <div>
            <label class="ui-label">Harga per malam</label>
            <input type="number" step="1" wire:model="harga" class="ui-input" />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['harga'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div>
            <label class="ui-label">Status</label>
            <select wire:model.live="status" class="ui-select">
                <option value="available">available</option>
                <option value="maintenance">maintenance</option>
            </select>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($status === 'maintenance'): ?>
                <button type="button" wire:click="openMaintenanceModal" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-amber-700 hover:text-amber-800">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    Atur Jadwal Maintenance (<?php echo e($this->maintenances->count()); ?>)
                </button>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fasilitasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" value="<?php echo e($f['id']); ?>" wire:model="fasilitas_ids" class="ui-checkbox">
                    <span><?php echo e($f['nama']); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fasilitas_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Foto Kamar (tambahkan atau hapus)</label>
            <input type="file" multiple wire:model="images" accept="image/*" class="ui-input file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" />
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kamar->fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="relative group">
                        <div class="aspect-[4/3] overflow-hidden rounded border bg-slate-100">
                            <img src="<?php echo e(asset('storage/'.ltrim($f->path,'/'))); ?>" class="h-full w-full object-cover" />
                        </div>
                        <button type="button" wire:click="deleteFoto(<?php echo e($f->id); ?>)" class="absolute top-1 right-1 hidden group-hover:inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 bg-white/90 hover:bg-rose-50">Hapus</button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-slate-500 text-sm">Belum ada foto.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($images): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $preview): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="relative group">
                            <div class="aspect-[4/3] bg-slate-100 rounded overflow-hidden flex items-center justify-center text-slate-400">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(method_exists($preview,'temporaryUrl')): ?>
                                    <img src="<?php echo e($preview->temporaryUrl()); ?>" class="h-full w-full object-cover" />
                                <?php else: ?>
                                    <span>Preview</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <button type="button" wire:click="removeImage(<?php echo e($i); ?>)" class="absolute top-1 right-1 hidden group-hover:inline-flex items-center rounded-md px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200 bg-white/90 hover:bg-rose-50">Hapus</button>
                            <span class="absolute bottom-1 left-1 inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-medium text-sky-700 ring-1 ring-inset ring-sky-200 bg-white/90">Baru</span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div class="sm:col-span-2 flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="<?php echo e(route('admin.kamar.index')); ?>" class="ui-btn-secondary">Batal</a>
        </div>
    </form>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMaintenanceModal): ?>
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

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-lg border border-sky-100 bg-sky-50/50">
                <div>
                    <label class="ui-label">Tanggal Mulai</label>
                    <input type="date" wire:model="mt_tanggal_mulai" class="ui-input" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mt_tanggal_mulai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label class="ui-label">Tanggal Selesai</label>
                    <input type="date" wire:model="mt_tanggal_selesai" class="ui-input" />
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mt_tanggal_selesai'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="sm:col-span-2">
                    <label class="ui-label">Keterangan Maintenance <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <textarea wire:model="mt_keterangan" rows="2" class="ui-textarea" placeholder="Contoh: Perbaikan AC, Renovasi kamar mandi, dll."></textarea>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['mt_keterangan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="sm:col-span-2 flex items-center gap-3">
                    <button type="button" wire:click="saveMaintenance" class="ui-btn-primary">
                        <?php echo e($editingMaintenanceId ? 'Update Jadwal' : 'Tambah Jadwal'); ?>

                    </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingMaintenanceId): ?>
                        <button type="button" wire:click="resetMaintenanceForm" class="ui-btn-secondary">Batal Edit</button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->maintenances->isNotEmpty()): ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="<?php echo e($editingMaintenanceId === $mt->id ? 'bg-sky-50' : ''); ?>">
                            <td class="py-2 px-3"><?php echo e($mt->tanggal_mulai->format('d M Y')); ?></td>
                            <td class="py-2 px-3"><?php echo e($mt->tanggal_selesai->format('d M Y')); ?></td>
                            <td class="py-2 px-3 text-slate-600"><?php echo e($mt->keterangan ?: '-'); ?></td>
                            <td class="py-2 px-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mt->tanggal_selesai->isPast()): ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-200">Selesai</span>
                                <?php elseif($mt->tanggal_mulai->isFuture()): ?>
                                    <span class="inline-flex items-center rounded-full bg-sky-50 px-2 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200">Terjadwal</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Sedang Berlangsung</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="py-2 px-3 text-right">
                                <button type="button" wire:click="editMaintenance(<?php echo e($mt->id); ?>)" class="ui-btn-secondary">Edit</button>
                                <button type="button" onclick="if(!confirm('Hapus jadwal maintenance ini?')){event.stopImmediatePropagation()}" wire:click="deleteMaintenance(<?php echo e($mt->id); ?>)" class="ml-1 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p class="mt-4 text-sm text-slate-500">Belum ada jadwal maintenance untuk kamar ini.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-5 flex justify-end">
                <button type="button" wire:click="closeMaintenanceModal" class="ui-btn-secondary">Tutup</button>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/admin/kamar/edit.blade.php ENDPATH**/ ?>