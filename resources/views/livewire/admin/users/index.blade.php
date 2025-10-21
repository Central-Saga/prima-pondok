<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $q = '';

    public function updatingQ(): void { $this->resetPage(); }

    public function getItemsProperty()
    {
        return User::query()
            ->when($this->q !== '', fn($q) => $q->where(function($x){
                $x->where('name','like','%'.$this->q.'%')
                  ->orWhere('email','like','%'.$this->q.'%');
            }))
            ->orderBy('name')
            ->paginate(10);
    }
}; ?>

<section class="ui-page">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pengguna</h1>
            <p class="mt-1 ui-help">Kelola akun, peran, dan profil wisatawan.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="text" wire:model.live.debounce.1000ms="q" placeholder="Cari nama/email" class="ui-input w-56" />
            <a href="{{ route('admin.users.create') }}" class="ui-btn-primary">Tambah Pengguna</a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-sky-100 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="py-2 px-3 text-left">Nama</th>
                    <th class="py-2 px-3 text-left">Email</th>
                    <th class="py-2 px-3 text-left">Role</th>
                    <th class="py-2 px-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($this->items as $u)
                    <tr>
                        <td class="py-2 px-3">{{ $u->name }}</td>
                        <td class="py-2 px-3">{{ $u->email }}</td>
                        <td class="py-2 px-3">
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($u->getRoleNames() as $role)
                                    <x-status-badge :status="$role" />
                                @endforeach
                            </div>
                        </td>
                        <td class="py-2 px-3 text-right">
                            <a href="{{ route('admin.users.edit', $u->id) }}" class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-200 hover:bg-sky-50">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->items->links() }}
    </div>
</section>
