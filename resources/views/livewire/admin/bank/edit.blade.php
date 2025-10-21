<?php

use App\Models\Bank;
use Livewire\Volt\Component;

new class extends Component {
    public Bank $bank;

    public string $nama_bank = '';
    public string $no_transfer = '';
    public string $status = 'active';

    public function mount(Bank $bank): void
    {
        $this->bank = $bank;
        $this->nama_bank = $bank->nama_bank;
        $this->no_transfer = $bank->no_transfer;
        $this->status = $bank->status;
    }

    public function save(): void
    {
        $data = $this->validate([
            'nama_bank' => 'required|string|max:100',
            'no_transfer' => 'required|string|max:100',
            'status' => 'required|string',
        ]);
        $this->bank->update($data);
        $this->redirectRoute('admin.bank.index');
    }
}; ?>

<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div>
        <h1 class="text-2xl font-semibold text-slate-900">Edit Bank</h1>
        <p class="mt-1 ui-help">Perbarui data bank.</p>
    </div>

    <form wire:submit="save" class="mt-6 space-y-4 ui-card">
        <div>
            <label class="ui-label">Nama Bank</label>
            <input type="text" wire:model="nama_bank" class="ui-input" />
            @error('nama_bank') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">No Transfer</label>
            <input type="text" wire:model="no_transfer" class="ui-input font-mono" />
            @error('no_transfer') <div class="ui-error">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="ui-label">Status</label>
            <select wire:model="status" class="ui-select w-40">
                <option value="active">active</option>
                <option value="hidden">hidden</option>
            </select>
        </div>
        <div class="flex items-center gap-3">
            <button class="ui-btn-primary">Update</button>
            <a href="{{ route('admin.bank.index') }}" class="ui-btn-secondary">Batal</a>
        </div>
    </form>
</section>

