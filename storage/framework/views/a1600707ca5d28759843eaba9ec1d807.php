<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\WithPagination;

?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pemesanan</h1>
            <p class="mt-1 ui-help">Daftar pemesanan terbaru. Gunakan filter status untuk menyaring.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari tamu/kamar/#id" class="ui-input w-56" />
            <select wire:model.live.debounce.1000ms="status" class="ui-select">
                <option value="all">Semua</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
                <option value="completed">Completed</option>
                <option value="extend">Extend</option>
            </select>
            <a href="<?php echo e(route('admin.pemesanan.create')); ?>" class="ui-btn-primary">Tambah</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Kode</th>
                    <th class="py-2 px-3 text-left">Kamar</th>
                    <th class="py-2 px-3 text-left">Wisatawan</th>
                    <th class="py-2 px-3 text-left">Check-in</th>
                    <th class="py-2 px-3 text-left">Check-out</th>
                    <th class="py-2 px-3 text-left">Total</th>
                    <th class="py-2 px-3 text-left">Status</th>
                    <th class="py-2 px-3 text-left">Checkout</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="<?php echo e($row->id === $highlightId ? 'bg-sky-100 ring-2 ring-sky-400' : ''); ?>">
                    <td class="py-2 px-3">#<?php echo e($row->id); ?></td>
                    <td class="py-2 px-3"><?php echo e($row->kamar->nama_kamar); ?></td>
                    <td class="py-2 px-3"><?php echo e($row->wisatawan->name ?? '-'); ?></td>
                    <td class="py-2 px-3"><?php echo e($row->tanggal_checkin->format('d M Y')); ?></td>
                    <td class="py-2 px-3"><?php echo e($row->tanggal_checkout->format('d M Y')); ?></td>
                    <td class="py-2 px-3">Rp <?php echo e(number_format($row->total_bayar,0,',','.')); ?></td>
                    <td class="py-2 px-3">
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
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
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->is_extend): ?>
                            <span class="ml-1 inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">EXT</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-2 px-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND])): ?>
                            <?php $coStatus = $row->checkout_status; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coStatus === 'diperingati'): ?>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 animate-pulse">
                                    ⚠ Diperingati
                                </span>
                            <?php elseif($coStatus === 'jatuh_tempo'): ?>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 animate-pulse">
                                    🔴 Jatuh Tempo
                                </span>
                            <?php elseif($coStatus === 'extend'): ?>
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">
                                    🔄 Extend
                                </span>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <span class="text-xs text-slate-400">—</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                    <td class="py-2 px-3 text-right">
                        <button type="button" wire:click="openDetail(<?php echo e($row->id); ?>)" class="ui-btn-secondary">Detail</button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND])): ?>
                            <button wire:click="confirmCheckout(<?php echo e($row->id); ?>)" class="ml-2 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-500">Check-out</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!in_array($row->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_COMPLETED, \App\Models\Pemesanan::STATUS_EXTEND])): ?>
                        <button onclick="if(!confirm('Hapus pemesanan ini?')){event.stopImmediatePropagation()}" wire:click="delete(<?php echo e($row->id); ?>)" class="ml-2 inline-flex items-center rounded-lg px-4 py-2 text-sm font-medium text-rose-700 ring-1 ring-inset ring-rose-200 hover:bg-rose-50">Hapus</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <?php echo e($this->items->links()); ?>

    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($checkoutId): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" wire:click="cancelCheckout" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
            <div role="dialog" aria-modal="true" class="relative w-full max-w-md rounded-2xl bg-white p-5 shadow-xl">
                <div class="text-lg font-semibold text-slate-900">Konfirmasi Check-out</div>
                <p class="mt-1 text-sm text-slate-600">Tandai booking ini sebagai <span class="font-medium">completed</span> (check-out)?</p>
                <div class="mt-5 flex items-center justify-end gap-2">
                    <button type="button" wire:click="cancelCheckout" class="ui-btn-secondary">Batal</button>
                    <button type="button" wire:click="doCheckout" class="ui-btn-primary !bg-emerald-600 hover:!bg-emerald-500">Ya, Check-out</button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailId && $detail): ?>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" wire:click="closeDetail" class="absolute inset-0 bg-slate-900/50" aria-label="Close"></button>
            <div role="dialog" aria-modal="true" class="relative w-full max-w-4xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Detail Pemesanan #<?php echo e($detail->id); ?></h2>
                        <p class="mt-1 text-sm text-slate-600">Kelola status dan lihat informasi lengkap pemesanan.</p>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 rounded-2xl border border-sky-100 bg-white p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <div class="text-slate-500">Kamar</div>
                                <div class="mt-1 font-semibold text-slate-900"><?php echo e($detail->kamar->nama_kamar); ?></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Wisatawan</div>
                                <div class="mt-1 font-semibold text-slate-900"><?php echo e($detail->wisatawan->name ?? '-'); ?></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Check-in</div>
                                <div class="mt-1 font-semibold text-slate-900"><?php echo e($detail->tanggal_checkin->format('d M Y')); ?></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Check-out</div>
                                <div class="mt-1 font-semibold text-slate-900"><?php echo e($detail->tanggal_checkout->format('d M Y')); ?></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Jumlah Malam</div>
                                <div class="mt-1 font-semibold text-slate-900"><?php echo e($detail->jumlah_hari); ?></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Total Bayar</div>
                                <div class="mt-1 font-semibold text-slate-900">Rp <?php echo e(number_format($detail->total_bayar,0,',','.')); ?></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $detail->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detail->status)]); ?>
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
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detail->is_extend): ?>
                                <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-200">
                                    Extend dari #<?php echo e($detail->extend_from_id); ?>

                                </span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($detail->status, [\App\Models\Pemesanan::STATUS_CONFIRMED, \App\Models\Pemesanan::STATUS_EXTEND])): ?>
                                <?php $coStatus = $detail->checkout_status; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coStatus === 'diperingati'): ?>
                                    <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200 animate-pulse">⚠ Diperingati</span>
                                <?php elseif($coStatus === 'jatuh_tempo'): ?>
                                    <span class="ml-2 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-200 animate-pulse">🔴 Jatuh Tempo</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <?php
                            $latestPayment = ($detail->pembayaran ?? collect())->sortByDesc('id')->first();
                            $proof = $latestPayment?->bukti_pembayaran;
                            $proofUrl = null;
                            if ($proof) {
                                $proofUrl = \Illuminate\Support\Str::startsWith($proof, ['http://','https://'])
                                    ? $proof
                                    : url('media/'.$proof);
                            }
                            $lowerProof = $proof ? strtolower($proof) : '';
                            $isImage = $proof && \Illuminate\Support\Str::endsWith($lowerProof, ['.jpg','.jpeg','.png','.gif','.webp']);
                        ?>
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm">
                            <div class="font-medium text-slate-900">Bukti Pembayaran</div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proofUrl): ?>
                                <div class="mt-2 flex items-center justify-between gap-3">
                                    <div class="text-slate-600"><?php echo e($latestPayment?->metode_bayar ?? '-'); ?> • <?php echo e($latestPayment?->status ?? '-'); ?></div>
                                    <a href="<?php echo e($proofUrl); ?>" target="_blank" rel="noopener" class="text-sky-700 hover:underline">Lihat File</a>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isImage): ?>
                                    <a href="<?php echo e($proofUrl); ?>" target="_blank" rel="noopener" class="mt-3 block">
                                        <img src="<?php echo e($proofUrl); ?>" alt="Bukti pembayaran" class="w-full max-h-72 rounded-lg border object-contain bg-white" />
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?>
                                <div class="mt-2 text-slate-600">Belum ada bukti pembayaran.</div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-sky-100 bg-white p-5">
                        <h3 class="text-base font-semibold text-slate-900">Aksi</h3>
                        <div class="mt-4 space-y-3 text-sm">
                            <div>
                                <label class="ui-label">Status</label>
                                <select wire:model.live="detailStatus" class="ui-select">
                                    <option value="pending">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($detailStatus === \App\Models\Pemesanan::STATUS_CANCELLED): ?>
                                <div>
                                    <label class="ui-label">Kategori Pembatalan <span class="text-rose-600">*</span></label>
                                    <select wire:model.live="detailCancelCategory" class="ui-select">
                                        <option value="bukti_pembayaran">Bukti Pembayaran</option>
                                        <option value="general">General</option>
                                    </select>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['detailCancelCategory'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div>
                                    <label class="ui-label">Catatan Pembatalan <span class="text-rose-600">*</span></label>
                                    <textarea wire:model.live="detailNote" rows="4" class="ui-input" placeholder="Tulis alasan pembatalan..."></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['detailNote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-2">
                            <button type="button" wire:click="closeDetail" class="ui-btn-secondary">Kembali</button>
                            <button type="button" wire:click="saveDetail" class="ui-btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/admin/pemesanan/index.blade.php ENDPATH**/ ?>