<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.split')] class extends Component {
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Lupa kata sandi')" :description="__('Masukkan email Anda untuk menerima tautan reset kata sandi')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
    <form method="POST" wire:submit="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Alamat email')"
            type="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white" data-test="email-password-reset-link-button">
            {{ __('Kirim tautan reset kata sandi') }}
        </flux:button>
    </form>
    </div>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Atau, kembali ke') }}</span>
        <flux:link :href="route('login')" class="text-sky-700 hover:underline" wire:navigate>{{ __('Masuk') }}</flux:link>
    </div>
</div>
