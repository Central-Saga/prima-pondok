<?php

use App\Models\Kamar;
use Livewire\Volt\Component;
use Livewire\WithPagination;

?>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Kamar</h1>
            <p class="mt-1 text-slate-600 text-sm">Kelola data kamar dan status ketersediaan.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari kamar..." class="ui-input w-56" />
            <a href="<?php echo e(route('admin.kamar.create')); ?>" class="ui-btn-primary">Tambah Kamar</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Nama</th>
                    <th class="py-2 px-3 text-left">Tipe</th>
                    <th class="py-2 px-3 text-left">Harga</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoop($loop->index); ?><?php endif; ?>
                    <tr>
                        <td class="py-2 px-3"><?php echo e($row->nama_kamar); ?></td>
                        <td class="py-2 px-3"><?php echo e($row->tipe_kamar); ?></td>
                        <td class="py-2 px-3">Rp <?php echo e(number_format($row->harga,0,',','.')); ?></td>
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
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

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
                            <a href="<?php echo e(route('admin.kamar.edit', $row->id)); ?>" class="ui-btn-secondary">Edit</a>
                            <button onclick="if(!confirm('Hapus kamar ini?')){event.stopImmediatePropagation()}" wire:click="delete(<?php echo e($row->id); ?>)" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($this->items->links()); ?>

    </div>
</section><?php /**PATH /Users/macpro/Documents/projectweb/prima-pondok/resources/views/livewire/admin/kamar/index.blade.php ENDPATH**/ ?>