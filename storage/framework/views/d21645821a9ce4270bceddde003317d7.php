<?php

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\KamarMaintenance;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-7">
                <div class="relative aspect-[16/9] lg:aspect-[21/9] rounded-xl overflow-hidden border bg-slate-100">
                    <?php ($fotoCount = ($kamar->fotos ?? collect())->count()); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoCount > 0): ?>
                        <?php ($main = $kamar->fotos[$activeIndex] ?? $kamar->fotos->first()); ?>
                        <img src="<?php echo e(asset('storage/'.ltrim($main->path,'/'))); ?>" alt="<?php echo e($kamar->nama_kamar); ?>" class="h-full w-full object-cover" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fotoCount > 1): ?>
                            <button type="button" wire:click="prevImage" class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow">&lsaquo;</button>
                            <button type="button" wire:click="nextImage" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 hover:bg-white p-2 shadow">&rsaquo;</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="h-full w-full flex items-center justify-center text-slate-400"><?php echo e(__('rooms.no_photo_fallback')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php ($hasPhotos = ($kamar->fotos ?? collect())->isNotEmpty()); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasPhotos): ?>
                    <div class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kamar->fotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" wire:click="select(<?php echo e($i); ?>)" class="aspect-[4/3] rounded-lg overflow-hidden border bg-slate-100 ring-2 transition <?php echo e($activeIndex === $i ? 'ring-sky-400' : 'ring-transparent hover:ring-slate-300'); ?>">
                                <img src="<?php echo e(asset('storage/'.ltrim($f->path,'/'))); ?>" alt="<?php echo e(__('rooms.no_photo')); ?> <?php echo e($i+1); ?>" class="h-full w-full object-cover" />
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="mt-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900"><?php echo e($kamar->nama_kamar); ?></h1>
                    <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $kamar->getDisplayStatus(),'class' => 'text-sm inline-block mt-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kamar->getDisplayStatus()),'class' => 'text-sm inline-block mt-2']); ?>
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
                    
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-slate-600">
                        <span><?php echo e(__('rooms.type_label')); ?> <span class="font-medium text-slate-900"><?php echo e($kamar->tipe_kamar ?: __('rooms.type_default')); ?></span></span>
                        <span class="inline-flex items-center rounded-full bg-sky-50 px-3 py-1 text-sm font-semibold text-sky-700 ring-1 ring-inset ring-sky-200">Rp <?php echo e(number_format($kamar->harga,0,',','.')); ?> <?php echo e(__('rooms.price_badge_suffix')); ?></span>
                    </div>

                    <?php ($hasFasilitas = ($kamar->fasilitas ?? collect())->isNotEmpty()); ?>
                    <?php ($colors = ['emerald','sky','amber','violet','rose','indigo']); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasFasilitas): ?>
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-slate-900"><?php echo e(__('rooms.facilities_title')); ?></h2>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kamar->fasilitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php ($c = $colors[$loop->index % count($colors)]); ?>
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset <?php echo e('bg-'.$c.'-50 text-'.$c.'-700 ring-'.$c.'-200'); ?>"><?php echo e($f->nama); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kamar->deskripsi): ?>
                        <div class="mt-4">
                            <h2 class="text-lg font-semibold text-slate-900"><?php echo e(__('rooms.description_title')); ?></h2>
                            <div class="mt-2 whitespace-pre-line text-slate-700 leading-relaxed text-justify"><?php echo e($kamar->deskripsi); ?></div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <div class="lg:col-span-5">
                <?php ($bookingDisabled = $kamar->isUnavailable()); ?>
                <?php ($activeMaintenanceInfo = $kamar->getActiveMaintenanceInfo()); ?>
                <?php ($upcomingMaintenances = $kamar->maintenances()->where('tanggal_selesai', '>=', now()->toDateString())->orderBy('tanggal_mulai')->get()); ?>
                <div id="booking" class="mt-2 lg:mt-0 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm lg:sticky lg:top-24">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900"><?php echo e(__('rooms.book_card_title')); ?></h3>
                            <p class="mt-1 text-sm text-slate-600"><?php echo e(__('rooms.book_card_subtitle')); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $kamar->getDisplayStatus()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($kamar->getDisplayStatus())]); ?>
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

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bookingDisabled): ?>
                    <div class="mt-4 rounded-lg p-4 bg-amber-50 border border-amber-200">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-900"><?php echo e(__('rooms.status_unavailable_title')); ?></h4>
                                <p class="mt-1 text-sm text-amber-700"><?php echo e($messageText); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeMaintenanceInfo && $activeMaintenanceInfo->keterangan): ?>
                                    <p class="mt-1 text-sm text-amber-800 font-medium"><?php echo e($activeMaintenanceInfo->keterangan); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="mt-2 text-sm text-amber-600"><?php echo e(__('rooms.contact_admin_help')); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upcomingMaintenances->isNotEmpty()): ?>
                    <div class="mt-4 rounded-lg p-4 bg-amber-50/60 border border-amber-100">
                        <div class="flex items-start gap-2 mb-2">
                            <svg class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384-3.11A1.5 1.5 0 015 10.82V6.75a3.75 3.75 0 017.5 0v4.07a1.5 1.5 0 01-1.036 1.43l-5.384 3.11zM15.75 6.75v4.07" />
                            </svg>
                            <h4 class="text-sm font-semibold text-amber-800"><?php echo e(__('rooms.maintenance_schedule_title')); ?></h4>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $upcomingMaintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-start gap-2 text-sm <?php echo e(!$loop->first ? 'mt-2 pt-2 border-t border-amber-100' : ''); ?>">
                            <span class="font-medium text-amber-700"><?php echo e($mt->tanggal_mulai->format('d M Y')); ?> — <?php echo e($mt->tanggal_selesai->format('d M Y')); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mt->keterangan): ?>
                            <p class="text-sm text-amber-600 mt-0.5"><?php echo e($mt->keterangan); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div>
                        <button type="button" wire:click="openCalendar" class="w-full rounded-lg border-2 border-dashed border-sky-300 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-700 hover:bg-sky-100 transition-colors">
                            <svg class="inline-block h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0121 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                            </svg>
                            <?php echo e(__('rooms.choose_date_calendar')); ?>

                        </button>
                    </div>

                    <!-- Calendar Section (Non-Modal) -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCalendar): ?>
                    <div class="mt-6 p-6 border-2 border-sky-200 rounded-xl bg-gradient-to-br from-white to-sky-50 shadow-lg">
                        <!-- Month Navigation -->
                        <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-sky-100">
                            <button type="button" wire:click="previousMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">‹</button>
                            <h3 class="text-xl font-bold text-slate-800"><?php echo e(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$currentMonth - 1]); ?> <?php echo e($currentYear); ?></h3>
                            <button type="button" wire:click="nextMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">›</button>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-2 mb-6">
                            <!-- Day Headers -->
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-center font-bold text-slate-600 py-2 text-xs uppercase tracking-wide"><?php echo e($dayName); ?></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <!-- Calendar Days -->
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getCalendarDays(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php echo $this->renderDayCell($day); ?>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <!-- Date Summary -->
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggal_checkin): ?>
                            <div class="bg-gradient-to-r from-emerald-50 to-sky-50 p-5 rounded-xl border-2 border-emerald-200 mb-4 shadow-md">
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h4 class="font-bold text-slate-800"><?php echo e(__('rooms.booking_summary')); ?></h4>
                                </div>
                                <p class="text-sm text-slate-700"><strong class="text-emerald-700"><?php echo e(__('booking.checkin')); ?>:</strong> <?php echo e($tanggal_checkin); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggal_checkout): ?>
                                    <p class="text-sm text-slate-700 mt-1"><strong class="text-emerald-700"><?php echo e(__('booking.checkout')); ?>:</strong> <?php echo e($tanggal_checkout); ?></p>
                                    <div class="mt-3 pt-3 border-t border-emerald-200">
                                        <p class="text-sm text-slate-700"><strong class="text-sky-700"><?php echo e(__('rooms.nights_label')); ?>:</strong> <?php echo e($jumlah_hari); ?> <?php echo e(trans_choice('booking.nights', $jumlah_hari)); ?></p>
                                        <p class="text-base font-bold text-slate-900 mt-1"><strong class="text-sky-700"><?php echo e(__('booking.total')); ?>:</strong> Rp <?php echo e(number_format($total_bayar, 0, ',', '.')); ?></p>
                                    </div>
                                    <!-- Action Buttons -->
                                    <div class="mt-4 pt-4 border-t border-emerald-200 flex gap-3">
                                        <button type="button" wire:click="pesan" class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <?php echo e(__('rooms.book_now')); ?>

                                        </button>
                                        <button type="button" wire:click="resetDates" class="flex-1 bg-gradient-to-r from-slate-500 to-slate-600 text-white font-semibold py-2.5 px-4 rounded-lg hover:from-slate-600 hover:to-slate-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            <?php echo e(__('rooms.reset_dates')); ?>

                                        </button>
                                    </div>
                                <?php else: ?>
                                    <p class="text-sm text-slate-500 mt-2 italic"><?php echo e(__('rooms.select_checkout_date')); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($messageText): ?>
                            <div class="rounded-md bg-rose-50 text-rose-700 p-3 text-sm mb-4"><?php echo e($messageText); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <button type="button" wire:click="closeCalendar" class="w-full bg-gradient-to-r from-slate-500 to-slate-600 text-white font-semibold py-3 rounded-lg hover:from-slate-600 hover:to-slate-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <?php echo e(__('rooms.close_calendar')); ?>

                        </button>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/public/kamar-show.blade.php ENDPATH**/ ?>