<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

?>

<main>
    <!-- Hero -->
    <section class="relative isolate overflow-hidden bg-gradient-to-b from-sky-50 to-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
            <div class="max-w-3xl">
                <h1 class="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl"><?php echo e(__('about.hero_title')); ?></h1>
                <p class="mt-6 text-lg leading-8 text-slate-700">
                    <?php echo e(__('about.hero_body_1')); ?>

                </p>
                <div class="mt-8 flex items-center gap-3">
                    <a href="<?php echo e(route('kamar.index')); ?>" class="ui-btn-primary"><?php echo e(__('about.hero_view_rooms')); ?></a>
                    <a href="<?php echo e(route('home')); ?>#kontak" class="ui-btn-secondary"><?php echo e(__('about.hero_contact_us')); ?></a>
                </div>
            </div>
        </div>
    </section>
  
    <!-- Video Placeholder -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl overflow-hidden border bg-slate-50">
                <div class="relative aspect-video bg-black" data-about-video>
                    <iframe
                        class="absolute inset-0 h-full w-full"
                        src="https://www.youtube-nocookie.com/embed/u5QDT1cTRbo?autoplay=1&mute=1&loop=1&playlist=u5QDT1cTRbo&controls=0&modestbranding=1&rel=0&playsinline=1&enablejsapi=1"
                        title="<?php echo e(__('about.video_title')); ?>"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>
                    <button
                        type="button"
                        class="absolute right-3 top-3 z-10 inline-flex items-center rounded-full bg-black/60 px-3 py-1.5 text-xs font-medium text-white backdrop-blur hover:bg-black/70 focus:outline-none focus:ring-2 focus:ring-white/70"
                        aria-pressed="true"
                        data-about-video-mute
                    >
                        Unmute
                    </button>
                </div>
            </div>
        </div>
    </section>

    <script>
        (() => {
            const root = document.querySelector('[data-about-video]');
            if (!root) return;

            const iframe = root.querySelector('iframe');
            const button = root.querySelector('[data-about-video-mute]');
            if (!iframe || !button) return;

            let muted = true;

            const post = (func) => {
                try {
                    iframe.contentWindow?.postMessage(
                        JSON.stringify({ event: 'command', func, args: [] }),
                        '*'
                    );
                } catch {
                    // no-op
                }
            };

            const render = () => {
                button.textContent = muted ? 'Unmute' : 'Mute';
                button.setAttribute('aria-pressed', muted ? 'true' : 'false');
            };

            button.addEventListener('click', () => {
                muted = !muted;
                post(muted ? 'mute' : 'unMute');
                render();
            });

            render();
        })();
    </script>

    <!-- Story -->
    <section class="py-12 sm:py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid gap-10 lg:grid-cols-2 items-start">
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900"><?php echo e(__('about.story_title')); ?></h2>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    <?php echo e(__('about.story_p1')); ?>

                </p>
                <p class="mt-4 text-slate-700 leading-relaxed">
                    <?php echo e(__('about.story_p2')); ?>

                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border overflow-hidden">
                    <img src="<?php echo e(asset('images/PYS08111.JPG')); ?>" alt="Our story photo 1" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                </div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border overflow-hidden">
                    <img src="<?php echo e(asset('images/PYS08130.JPG')); ?>" alt="Our story photo 2" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                </div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border overflow-hidden">
                    <img src="<?php echo e(asset('images/PYS08172.JPG')); ?>" alt="Our story photo 3" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                </div>
                <div class="aspect-[4/3] rounded-xl bg-slate-100 border overflow-hidden">
                    <img src="<?php echo e(asset('images/PYS08156.JPG')); ?>" alt="Our story photo 4" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                </div>
            </div>
        </div>
        </section>

    <!-- Values -->
    <section class="py-12 sm:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-semibold text-slate-900"><?php echo e(__('about.values_title')); ?></h2>
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold"><?php echo e(__('about.value_hospitality_title')); ?></div>
                    <p class="mt-2 text-slate-700"><?php echo e(__('about.value_hospitality_body')); ?></p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold"><?php echo e(__('about.value_cleanliness_title')); ?></div>
                    <p class="mt-2 text-slate-700"><?php echo e(__('about.value_cleanliness_body')); ?></p>
                </div>
                <div class="rounded-2xl bg-white ring-1 ring-slate-100 p-6">
                    <div class="text-slate-900 font-semibold"><?php echo e(__('about.value_responsiveness_title')); ?></div>
                    <p class="mt-2 text-slate-700"><?php echo e(__('about.value_responsiveness_body')); ?></p>
                </div>
            </div>
        </div>
        </section>

    <!-- Stats + CTA -->
    

    <!-- Contact reuse -->
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
</main><?php /**PATH C:\laragon\www\prima-pondok\resources\views\livewire/public/about.blade.php ENDPATH**/ ?>