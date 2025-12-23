<?php

use App\Models\User;
use App\Models\Wisatawan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth.split')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        event(new Registered(($user = User::create($validated))));

        // Assign default role and profile
        try {
            $user->assignRole('wisatawan');
        } catch (\Throwable $e) {
            // ignore if role not seeded yet
        }

        if (! $user->wisatawan) {
            $user->wisatawan()->create([
                'name' => $user->name,
                'status' => 'active',
            ]);
        }

        Auth::login($user);

        Session::regenerate();

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('auth.register_title')" :description="__('auth.register_description')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
    <form method="POST" wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('auth.name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('auth.name_placeholder')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('auth.email')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('auth.password_label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('auth.password_placeholder')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('auth.confirm_password_label')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('auth.confirm_password_placeholder')"
            viewable
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full bg-sky-600 hover:bg-sky-500 text-white" data-test="register-user-button">
                {{ __('auth.register_button') }}
            </flux:button>
        </div>
    </form>
    </div>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('auth.have_account') }}</span>
        <flux:link :href="route('login')" class="text-sky-700 hover:underline" wire:navigate>{{ __('auth.login_link') }}</flux:link>
    </div>
</div>
