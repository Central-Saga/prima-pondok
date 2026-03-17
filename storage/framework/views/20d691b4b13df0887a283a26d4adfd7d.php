<?php

use App\Models\Kamar;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

?>

<main>
<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900"><?php echo e(__('rooms.list_title')); ?></h1>
                <p class="mt-1 text-slate-600"><?php echo e(__('rooms.list_subtitle')); ?></p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" wire:model.live.debounce.800ms="q" class="ui-input w-64" placeholder="<?php echo e(__('rooms.search_placeholder')); ?>" />
                <a href="<?php echo e(route('home')); ?>" class="ui-btn-secondary"><?php echo e(__('rooms.back_home')); ?></a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="rounded-xl border border-sky-100 bg-white shadow-sm overflow-hidden transform-gpu transition duration-300 ease-out hover:scale-[1.02] hover:shadow-md hover:border-sky-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($item->fotos ?? collect())->isNotEmpty()): ?>
                        <div class="h-44 bg-slate-100">
                            <img src="<?php echo e(asset('storage/'.$item->fotos->first()->path)); ?>" alt="<?php echo e($item->nama_kamar); ?>" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='<?php echo e(asset('storage/login/login reg page.webp')); ?>'">
                        </div>
                    <?php else: ?>
                        <div class="h-44 bg-slate-100 flex items-center justify-center text-slate-400"><?php echo e(__('rooms.no_photo')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-slate-900"><?php echo e($item->nama_kamar); ?></h3>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200"><?php echo e($item->tipe_kamar ?: __('rooms.default_type')); ?></span>
                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $item->getDisplayStatus()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item->getDisplayStatus())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                        </div>
                        <p class="mt-2 font-semibold text-slate-900">Rp <?php echo e(number_format($item->harga, 0, ',', '.')); ?><?php echo e(__('rooms.price_suffix')); ?></p>
                        <div class="mt-4">
                            <a href="<?php echo e(route('kamar.show', $item->id)); ?>" class="ui-btn-primary"><?php echo e(__('rooms.view_detail')); ?></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-slate-600"><?php echo e(__('rooms.no_rooms')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="mt-6">
            <?php echo e($this->items->links()); ?>

        </div>
    </div>
    </section>

    <?php if (isset($component)) { $__componentOriginal647b175ed9e08e7e2da03809063e0558 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal647b175ed9e08e7e2da03809063e0558 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.contact-section','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('contact-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal647b175ed9e08e7e2da03809063e0558)): ?>
<?php $attributes = $__attributesOriginal647b175ed9e08e7e2da03809063e0558; ?>
<?php unset($__attributesOriginal647b175ed9e08e7e2da03809063e0558); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal647b175ed9e08e7e2da03809063e0558)): ?>
<?php $component = $__componentOriginal647b175ed9e08e7e2da03809063e0558; ?>
<?php unset($__componentOriginal647b175ed9e08e7e2da03809063e0558); ?>
<?php endif; ?>
</main><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/public/kamar-index.blade.php ENDPATH**/ ?>