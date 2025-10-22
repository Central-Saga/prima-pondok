<x-layouts.auth.split>
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Konfirmasi kata sandi')"
            :description="__('Ini area aman aplikasi. Harap konfirmasi kata sandi sebelum melanjutkan.')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-6">
            @csrf

            <flux:input
                name="password"
                :label="__('Kata sandi')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Kata sandi')"
                viewable
            />

            <flux:button variant="primary" type="submit" class="w-full bg-sky-600 hover:bg-sky-500 text-white" data-test="confirm-password-button">
                {{ __('Konfirmasi') }}
            </flux:button>
        </form>
        </div>
    </div>
</x-layouts.auth.split>
