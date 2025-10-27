<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.split')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => $this->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status !== Password::PasswordReset) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Reset kata sandi')" :description="__('Silakan masukkan kata sandi baru Anda di bawah ini')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
    <form method="POST" wire:submit="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Alamat email')"
            type="email"
            required
            autocomplete="email"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Kata sandi')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Konfirmasi kata sandi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Konfirmasi kata sandi')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full bg-sky-600 hover:bg-sky-500 text-white" data-test="reset-password-button">
                {{ __('Reset kata sandi') }}
            </flux:button>
        </div>
    </form>
    </div>
</div>
