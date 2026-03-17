<?php

use App\Models\Review;
use Livewire\Volt\Component;
use Livewire\WithPagination;

?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Review</h1>
            <p class="mt-1 text-slate-600 text-sm">Kelola review pelanggan dan tentukan yang tampil di landing page.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari kamar/nama/komentar..." class="ui-input w-48 sm:w-64" />
            <select wire:model.live.debounce.1000ms="status" class="ui-select w-32 sm:w-40">
                <option value="all">Semua</option>
                <option value="published">Tampil</option>
                <option value="hidden">Tersembunyi</option>
            </select>
            <a href="<?php echo e(route('admin.review.create')); ?>" class="inline-flex items-center justify-center rounded-lg bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-500 shadow-sm transition-colors">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Review
            </a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Booking</th>
                    <th class="py-2 px-3 text-left">Kamar</th>
                    <th class="py-2 px-3 text-left">Pelanggan</th>
                    <th class="py-2 px-3 text-left">Rating</th>
                    <th class="py-2 px-3 text-left">Tampil</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td class="py-2 px-3">#<?php echo e($row->pemesanan_id); ?></td>
                        <td class="py-2 px-3"><?php echo e($row->kamar->nama_kamar ?? '-'); ?></td>
                        <td class="py-2 px-3"><?php echo e($row->wisatawan->name ?? '-'); ?></td>
                        <td class="py-2 px-3">
                            <div class="flex items-center gap-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i=1; $i<=5; $i++): ?>
                                    <svg class="h-4 w-4 <?php echo e($row->rating >= $i ? 'text-amber-400' : 'text-slate-200'); ?>" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118l-2.8-2.034a1 1 0 0 0-1.176 0l-2.8 2.034c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.71c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/>
                                    </svg>
                                <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </td>
                        <td class="py-2 px-3">
                            <button wire:click="togglePublish(<?php echo e($row->id); ?>)" class="<?php echo e($row->is_published ? 'inline-flex items-center rounded-lg bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-100' : 'inline-flex items-center rounded-lg bg-slate-50 px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-100'); ?>">
                                <?php echo e($row->is_published ? 'Tampil' : 'Sembunyi'); ?>

                            </button>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <a href="<?php echo e(route('admin.review.edit', $row->id)); ?>" class="ui-btn-secondary">Edit</a>
                            <button onclick="if(!confirm('Hapus review ini?')){event.stopImmediatePropagation()}" wire:click="delete(<?php echo e($row->id); ?>)" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($this->items->links()); ?>

    </div>
</section><?php /**PATH /Users/macpro/Documents/projectweb/prima-pondok/resources/views/livewire/admin/review/index.blade.php ENDPATH**/ ?>