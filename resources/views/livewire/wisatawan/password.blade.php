<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function save(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('wisatawan'), 403);

        $this->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('status', __('account.password_saved'));
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('account.password_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('account.password_subtitle') }}</p>
            </div>
            <a href="{{ route('wisatawan.profile') }}" class="ui-btn-secondary">{{ __('account.back_profile') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="save" class="mt-6 space-y-4 ui-card">
            <div>
                <label class="ui-label">{{ __('account.current_password') }}</label>
                <input type="password" wire:model="current_password" class="ui-input" autocomplete="current-password" />
                @error('current_password') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="ui-label">{{ __('account.new_password') }}</label>
                <input type="password" wire:model="password" class="ui-input" autocomplete="new-password" />
                @error('password') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="ui-label">{{ __('account.new_password_confirm') }}</label>
                <input type="password" wire:model="password_confirmation" class="ui-input" autocomplete="new-password" />
            </div>
            <div class="flex items-center gap-3">
                <button class="ui-btn-primary">{{ __('account.save') }}</button>
                <a href="{{ route('booking.index') }}" class="ui-btn-secondary">{{ __('account.back_bookings') }}</a>
            </div>
        </form>
    </div>
</section>

