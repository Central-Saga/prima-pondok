<?php

use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900"><?php echo e(__('booking.history_title')); ?></h1>
                <p class="mt-1 text-sm text-slate-600"><?php echo e(__('booking.history_subtitle')); ?></p>
            </div>
            <div class="flex items-center gap-3">
                <select wire:model.live.debounce.1000ms="status" class="ui-select w-40">
                    <option value="all"><?php echo e(__('booking.filter_all')); ?></option>
                    <option value="pending"><?php echo e(__('booking.filter_pending')); ?></option>
                    <option value="confirmed"><?php echo e(__('booking.filter_confirmed')); ?></option>
                    <option value="completed"><?php echo e(__('booking.filter_completed')); ?></option>
                    <option value="cancelled"><?php echo e(__('booking.filter_cancelled')); ?></option>
                    <option value="extend"><?php echo e(__('booking.filter_extend')); ?></option>
                </select>
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.code')); ?></th>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.room')); ?></th>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.checkin')); ?></th>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.checkout')); ?></th>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.total_header')); ?></th>
                        <th class="py-2 px-3 text-left"><?php echo e(__('booking.status_header')); ?></th>
                        <th class="py-2 px-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="py-2 px-3">#<?php echo e($row->id); ?></td>
                            <td class="py-2 px-3"><?php echo e($row->kamar->nama_kamar ?? '-'); ?></td>
                            <td class="py-2 px-3"><?php echo e(optional($row->tanggal_checkin)->format('d M Y')); ?></td>
                            <td class="py-2 px-3"><?php echo e(optional($row->tanggal_checkout)->format('d M Y')); ?></td>
                            <td class="py-2 px-3">Rp <?php echo e(number_format($row->total_bayar,0,',','.')); ?></td>
                            <td class="py-2 px-3"><?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $row->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?></td>
                            <td class="py-2 px-3 text-right">
                                <?php
                                    $eligibleStatus = in_array($row->status, ['confirmed','completed'], true);
                                    $canReview = $eligibleStatus;
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $eligibleStatus): ?>
                                    <a href="<?php echo e(route('booking.show', $row->id)); ?>" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50">
                                        <?php echo e(__('booking.view_detail')); ?>

                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($eligibleStatus): ?>
                                    <a href="<?php echo e(route('booking.print', $row->id)); ?>" target="_blank" rel="noopener" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 hover:bg-emerald-50">
                                        <?php echo e(__('booking.print_proof')); ?>

                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canReview): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->review): ?>
                                        <span class="<?php echo e((! $eligibleStatus && ! empty($row->review)) ? 'ml-2' : ''); ?> inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200 bg-emerald-50">
                                            <?php echo e(__('reviews.thanks')); ?>

                                        </span>
                                    <?php else: ?>
                                        <a href="<?php echo e(route('booking.review', $row->id)); ?>" class="<?php echo e(! $eligibleStatus ? 'ml-2' : ''); ?> inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">
                                            <?php echo e(__('reviews.write')); ?>

                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td class="py-6 px-3 text-center text-slate-600" colspan="7"><?php echo e(__('booking.no_bookings')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <?php echo e($this->items->links()); ?>

        </div>
    </div>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/wisatawan/booking-index.blade.php ENDPATH**/ ?>