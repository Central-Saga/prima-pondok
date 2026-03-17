<?php

use App\Models\Pemesanan;
use App\Models\Kamar;
use App\Models\Wisatawan;
use App\Models\User;
use Livewire\Volt\Component;

?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Tambah Pemesanan</h1>
        <p class="mt-1 ui-help">Lengkapi data pemesanan secara manual.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="ui-label">Wisatawan</label>
                <input type="text" wire:model="wisatawan_nama" placeholder="Ketik nama wisatawan" class="ui-input" />
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['wisatawan_nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label class="ui-label">Kamar</label>
                <select wire:model.live="kamar_id" class="ui-select">
                    <option value="">-- pilih --</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $kamarList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($k['id']); ?>"><?php echo e($k['nama_kamar']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['kamar_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="ui-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <div>
            <button type="button" wire:click="openCalendar" <?php if(!$kamar_id): echo 'disabled'; endif; ?> class="w-full rounded-lg border-2 border-dashed border-sky-300 bg-sky-50 px-4 py-3 text-sm font-medium text-sky-700 hover:bg-sky-100 transition-colors enabled:hover:bg-sky-100 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-300">
                <svg class="inline-block h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0121 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$kamar_id): ?>
                    Pilih Kamar Terlebih Dahulu
                <?php else: ?>
                    Choose Date with Calendar
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </button>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kamar_id && $showCalendar): ?>
        <div class="mt-6 p-6 border-2 border-sky-200 rounded-xl bg-gradient-to-br from-white to-sky-50 shadow-lg">
            <!-- Month Navigation -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b-2 border-sky-100">
                <button type="button" wire:click="previousMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">‹</button>
                <h3 class="text-xl font-bold text-slate-800"><?php echo e(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$currentMonth - 1]); ?> <?php echo e($currentYear); ?></h3>
                <button type="button" wire:click="nextMonth" class="w-10 h-10 flex items-center justify-center rounded-full text-2xl font-bold text-sky-600 hover:bg-sky-100 hover:text-sky-700 transition-all duration-200 shadow-sm hover:shadow">›</button>
            </div>
            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-2 mb-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="text-center font-bold text-slate-600 py-2 text-xs uppercase tracking-wide"><?php echo e($dayName); ?></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getCalendarDays(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $this->renderDayCell($day); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <!-- Date Summary -->
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggal_checkin): ?>
                <div class="bg-gradient-to-r from-emerald-50 to-sky-50 p-5 rounded-xl border-2 border-emerald-200 mb-4 shadow-md">
                    <p class="text-sm text-slate-700"><strong class="text-emerald-700">Check-in:</strong> <?php echo e($tanggal_checkin); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggal_checkout): ?>
                        <p class="text-sm text-slate-700 mt-1"><strong class="text-emerald-700">Check-out:</strong> <?php echo e($tanggal_checkout); ?></p>
                        <div class="mt-3 pt-3 border-t border-emerald-200">
                            <p class="text-sm text-slate-700"><strong class="text-sky-700">Nights:</strong> <?php echo e($jumlah_hari); ?></p>
                            <p class="text-base font-bold text-slate-900 mt-1"><strong class="text-sky-700">Total:</strong> Rp <?php echo e(number_format($total_bayar, 0, ',', '.')); ?></p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <button type="button" wire:click="closeCalendar" class="w-full bg-gradient-to-r from-slate-500 to-slate-600 text-white font-semibold py-3 rounded-lg hover:from-slate-600 hover:to-slate-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <svg class="inline-block w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Close Calendar
            </button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tanggal_checkin && $tanggal_checkout): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-lg mb-6">
            <div>
                <label class="ui-label text-emerald-700">Check-in</label>
                <input type="date" wire:model="tanggal_checkin" class="ui-input bg-white" />
            </div>
            <div>
                <label class="ui-label text-emerald-700">Check-out</label>
                <input type="date" wire:model="tanggal_checkout" class="ui-input bg-white" />
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="ui-label">Jumlah Malam</label>
                <input type="number" wire:model="jumlah_hari" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Total Bayar</label>
                <input type="number" wire:model="total_bayar" class="ui-input" />
            </div>
            <div>
                <label class="ui-label">Status</label>
                <select wire:model="status" class="ui-select">
                    <option value="pending">pending</option>
                    <option value="confirmed">confirmed</option>
                    <option value="cancelled">cancelled</option>
                    <option value="completed">completed</option>
                </select>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Simpan</button>
            <a href="<?php echo e(route('admin.pemesanan.index')); ?>" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/admin/pemesanan/create.blade.php ENDPATH**/ ?>