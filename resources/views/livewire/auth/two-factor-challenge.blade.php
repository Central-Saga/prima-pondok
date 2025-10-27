<x-layouts.auth.split>
    <div class="flex flex-col gap-6">
        <div
            class="relative w-full h-auto"
            x-cloak
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;

                    this.code = '';
                    this.recovery_code = '';

                    $dispatch('clear-2fa-auth-code');
            
                    $nextTick(() => {
                        this.showRecoveryInput
                            ? this.$refs.recovery_code?.focus()
                            : $dispatch('focus-2fa-auth-code');
                    });
                },
            }"
        >
            <div x-show="!showRecoveryInput">
                <x-auth-header
                    :title="__('Kode autentikasi')"
                    :description="__('Masukkan kode autentikasi dari aplikasi autentikator Anda.')"
                />
            </div>

            <div x-show="showRecoveryInput">
                <x-auth-header
                    :title="__('Kode pemulihan')"
                    :description="__('Konfirmasikan akses ke akun Anda dengan memasukkan salah satu kode pemulihan darurat.')"
                />
            </div>

            <div class="rounded-2xl border border-sky-100 bg-white px-6 py-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-950">
            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <div class="space-y-5 text-center">
                    <div x-show="!showRecoveryInput">
                        <div class="flex items-center justify-center my-5">
                            <x-input-otp
                                name="code"
                                digits="6"
                                autocomplete="one-time-code"
                                x-model="code"
                            />
                        </div>

                        @error('code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <flux:input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                autocomplete="one-time-code"
                                x-model="recovery_code"
                            />
                        </div>

                        @error('recovery_code')
                            <flux:text color="red">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full bg-sky-600 hover:bg-sky-500 text-white"
                    >
                        {{ __('Lanjutkan') }}
                    </flux:button>
                </div>
                <div class="mt-5 space-x-0.5 text-sm leading-5 text-center text-zinc-600 dark:text-zinc-400">
                    <span class="opacity-80">{{ __('atau Anda bisa') }}</span>
                    <div class="inline font-medium underline cursor-pointer">
                        <span x-show="!showRecoveryInput" @click="toggleInput()">{{ __('masuk dengan kode pemulihan') }}</span>
                        <span x-show="showRecoveryInput" @click="toggleInput()">{{ __('masuk dengan kode autentikasi') }}</span>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</x-layouts.auth.split>
