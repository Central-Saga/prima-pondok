<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e(\App\Models\Setting::get('site_name', 'Pondok Teges')); ?></title>
        <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
        <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
    </head>
    <body class="min-h-screen bg-white text-slate-800">
        <?php echo $__env->make('partials.site-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasRole('wisatawan') && auth()->user()->wisatawan): ?>
                <?php
                    $__checkoutBooking = \App\Models\Pemesanan::with('kamar')
                        ->where('wisatawan_id', auth()->user()->wisatawan->id)
                        ->whereIn('status', [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND])
                        ->where('tanggal_checkout', '<=', now()->addDay()->toDateString())
                        ->where('tanggal_checkout', '>=', now()->toDateString())
                        ->orderBy('tanggal_checkout')
                        ->first();
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($__checkoutBooking): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($__checkoutBooking->isCheckoutOverdue()): ?>
                        
                        <div class="bg-rose-600 text-white px-4 py-3" id="checkout-overdue-banner">
                            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="h-6 w-6 flex-shrink-0 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    <span class="text-sm font-semibold">
                                        <?php echo e(__('booking.banner_overdue_text', ['amount' => number_format(\App\Models\Pemesanan::DENDA_AMOUNT, 0, ',', '.')])); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($__checkoutBooking->canExtend(1)): ?>
                                        <a href="<?php echo e(route('booking.extend', $__checkoutBooking->id)); ?>" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('booking.banner_overdue_extend')); ?></a>
                                    <?php else: ?>
                                        <button type="button" onclick="document.getElementById('global-extend-modal').classList.remove('hidden')" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-50"><?php echo e(__('booking.banner_overdue_extend')); ?></button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <a href="<?php echo e(route('booking.show', $__checkoutBooking->id)); ?>" class="inline-flex items-center rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/10"><?php echo e(__('booking.banner_overdue_detail')); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php elseif($__checkoutBooking->isCheckoutWarning()): ?>
                        
                        <div class="bg-amber-500 text-white px-4 py-3" id="checkout-warning-banner">
                            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-semibold">
                                        <?php echo e(__('booking.banner_warning_text', ['room' => $__checkoutBooking->kamar->nama_kamar])); ?>

                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($__checkoutBooking->canExtend(1)): ?>
                                        <a href="<?php echo e(route('booking.extend', $__checkoutBooking->id)); ?>" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50"><?php echo e(__('booking.banner_warning_extend')); ?></a>
                                    <?php else: ?>
                                        <button type="button" onclick="document.getElementById('global-extend-modal').classList.remove('hidden')" class="inline-flex items-center rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50"><?php echo e(__('booking.banner_warning_extend')); ?></button>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <a href="<?php echo e(route('booking.show', $__checkoutBooking->id)); ?>" class="inline-flex items-center rounded-md border border-white/30 px-3 py-1.5 text-xs font-semibold text-white hover:bg-white/10"><?php echo e(__('booking.banner_warning_detail')); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div id="global-extend-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50" onclick="this.parentElement.classList.add('hidden')"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
                    <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900"><?php echo e(__('booking.extend_unavailable_title')); ?></h3>
                <p class="mt-2 text-sm text-slate-600"><?php echo e(__('booking.extend_unavailable_message')); ?></p>
                <button type="button" onclick="this.closest('#global-extend-modal').classList.add('hidden')" class="mt-5 w-full rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-500"><?php echo e(__('booking.extend_unavailable_ok')); ?></button>
            </div>
        </div>

        <?php echo e($slot); ?>


        <?php echo $__env->make('partials.site-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </body>
</html>
<?php /**PATH C:\laragon\www\prima-pondok\resources\views/components/layouts/public.blade.php ENDPATH**/ ?>