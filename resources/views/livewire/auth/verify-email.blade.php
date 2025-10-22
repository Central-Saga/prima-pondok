<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.split')] class extends Component {
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }

    /**
     * Handle the component's rendering hook.
     */
    public function rendering(View $view): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }
    }
}; ?>

<div class="mt-4 flex flex-col gap-6">
    <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
        <flux:text class="text-center">
            {{ __('Silakan verifikasi alamat email Anda dengan mengklik tautan yang baru kami kirimkan ke email Anda.') }}
        </flux:text>

        @if (session('status') == 'verification-link-sent')
            <flux:text class="mt-3 text-center font-medium !dark:text-green-400 !text-green-600">
                {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat pendaftaran.') }}
            </flux:text>
        @endif

        <div class="mt-6 flex flex-col items-center justify-between space-y-3">
            <flux:button wire:click="sendVerification" variant="primary" class="w-full bg-sky-600 hover:bg-sky-500 text-white">
                {{ __('Kirim ulang email verifikasi') }}
            </flux:button>

            <flux:link class="text-sm cursor-pointer text-sky-700 hover:underline" wire:click="logout" data-test="logout-button">
                {{ __('Keluar') }}
            </flux:link>
        </div>
    </div>
</div>
