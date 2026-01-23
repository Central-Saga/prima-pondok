<?php

use App\Models\Wisatawan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.public')] class extends Component {
    public string $name = '';
    public string $email = '';
    public ?string $no_hp = null;
    public ?string $nationality = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('wisatawan'), 403);

        $this->name = (string) ($user->name ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->no_hp = $user->wisatawan?->no_hp;
        $this->nationality = $user->wisatawan?->nationality;
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_if(! $user || ! $user->hasRole('wisatawan'), 403);

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'nationality' => ['nullable', 'string', 'max:100'],
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $wisatawan = $user->wisatawan ?: $user->wisatawan()->create([
            'name' => $user->name,
            'status' => 'active',
        ]);

        $wisatawan->update([
            'name' => $data['name'],
            'no_hp' => $data['no_hp'],
            'nationality' => $data['nationality'],
        ]);

        session()->flash('status', __('account.profile_saved'));
    }
}; ?>

<section class="py-12 sm:py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">{{ __('account.profile_title') }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ __('account.profile_subtitle') }}</p>
            </div>
            <a href="{{ route('home') }}" class="ui-btn-secondary">{{ __('account.back_home') }}</a>
        </div>

        @if(session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="save" class="mt-6 space-y-4 ui-card">
            <div>
                <label class="ui-label">{{ __('account.name') }}</label>
                <input type="text" wire:model="name" class="ui-input" />
                @error('name') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="ui-label">{{ __('account.email') }}</label>
                <input type="email" wire:model="email" class="ui-input" />
                @error('email') <div class="ui-error">{{ $message }}</div> @enderror
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="ui-label">{{ __('account.phone') }}</label>
                    <input type="text" wire:model="no_hp" class="ui-input" placeholder="+62..." />
                    @error('no_hp') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="ui-label">{{ __('account.nationality') }}</label>
                    <input type="text" wire:model="nationality" class="ui-input" />
                    @error('nationality') <div class="ui-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="ui-btn-primary">{{ __('account.save') }}</button>
                <a href="{{ route('wisatawan.password') }}" class="ui-btn-secondary">{{ __('account.change_password') }}</a>
            </div>
        </form>
    </div>
</section>
