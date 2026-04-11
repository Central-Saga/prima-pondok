<?php

use App\Models\Pemesanan;
use App\Models\Review;
use Livewire\Volt\Component;

?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Tambah Review</h1>
            <p class="mt-1 text-slate-600 text-sm">Buat review manual untuk booking tertentu.</p>
        </div>
        <a href="<?php echo e(route('admin.review.index')); ?>" class="ui-btn-secondary">Kembali</a>
    </div>

    <div class="mt-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
        <form wire:submit="save" class="space-y-4 text-sm">
            <div>
                <label class="ui-label">ID Booking (Pemesanan)</label>
                <input type="number" wire:model.live.debounce.400ms="pemesanan_id" class="ui-input" placeholder="Contoh: 12" />
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['pemesanan_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bookingInfo): ?>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid sm:grid-cols-2 gap-2">
                        <div><span class="text-slate-600">Kamar:</span> <span class="font-medium text-slate-900"><?php echo e($bookingInfo['kamar']); ?></span></div>
                        <div><span class="text-slate-600">Pelanggan:</span> <span class="font-medium text-slate-900"><?php echo e($bookingInfo['wisatawan']); ?></span></div>
                        <div><span class="text-slate-600">Check-in:</span> <span class="font-medium text-slate-900"><?php echo e($bookingInfo['checkin'] ?? '-'); ?></span></div>
                        <div><span class="text-slate-600">Check-out:</span> <span class="font-medium text-slate-900"><?php echo e($bookingInfo['checkout'] ?? '-'); ?></span></div>
                    </div>
                    <div class="mt-2 text-xs text-slate-600">Status booking: <span class="capitalize"><?php echo e($bookingInfo['status']); ?></span></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <div class="ui-label">Rating</div>
                <div class="mt-2 flex items-center gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1; $i<=5; $i++): ?>
                        <label class="cursor-pointer">
                            <input type="radio" class="sr-only" wire:model.live="rating" value="<?php echo e($i); ?>">
                            <svg class="h-7 w-7 <?php echo e($rating >= $i ? 'text-amber-400' : 'text-slate-200'); ?>" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.71c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                            </svg>
                        </label>
                    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['rating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label class="ui-label">Komentar</label>
                <textarea wire:model.live.debounce.300ms="komentar" rows="5" class="ui-input" placeholder="Isi komentar..."></textarea>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['komentar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" />
                <span>Tampilkan di landing page</span>
            </label>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="<?php echo e(route('admin.review.index')); ?>" class="ui-btn-secondary">Batal</a>
                <button class="ui-btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</section><?php /**PATH /Users/macpro/Documents/projectweb/prima-pondok/resources/views/livewire/admin/review/create.blade.php ENDPATH**/ ?>