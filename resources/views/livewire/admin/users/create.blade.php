<?php

use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Validation\Rules;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = 'wisatawan';
    public ?string $no_hp = null;
    public ?string $nationality = null;

    public function save(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required','confirmed', Rules::password()->min(8)],
            'role' => 'required|in:admin,wisatawan',
            'no_hp' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:100',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        try { $user->syncRoles([$this->role]); } catch (\Throwable $e) {}

        if ($this->role === 'wisatawan') {
            $user->wisatawan()->create([
                'name' => $this->name,
                'no_hp' => $this->no_hp,
                'nationality' => $this->nationality,
                'status' => 'active',
            ]);
        }

        $this->redirectRoute('admin.users.index');
    }
}; ?>

<section class="ui-page">
    <h1 class="text-2xl font-semibold text-slate-900">Tambah Pengguna</h1>

    <form wire:submit="save" class="mt-6 ui-card grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2">
            <label class="ui-label">Nama</label>
            <input type="text" wire:model="name" class="ui-input" />
            @error('name') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Email</label>
            <input type="email" wire:model="email" class="ui-input" />
            @error('email') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Password</label>
            <input type="password" wire:model="password" class="ui-input" />
            @error('password') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Konfirmasi Password</label>
            <input type="password" wire:model="password_confirmation" class="ui-input" />
        </div>
        <div>
            <label class="ui-label">Role</label>
            <select wire:model="role" class="ui-select">
                <option value="wisatawan">Wisatawan</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div>
            <label class="ui-label">No HP (opsional)</label>
            <input type="text" wire:model="no_hp" class="ui-input" />
        </div>
        <div class="sm:col-span-2">
            <label class="ui-label">Kebangsaan (opsional)</label>
            <input type="text" wire:model="nationality" class="ui-input" />
        </div>
        <div class="sm:col-span-2">
            <button class="ui-btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>

