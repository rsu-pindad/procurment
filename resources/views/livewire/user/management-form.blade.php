<?php

use Livewire\Volt\Component;
use Illuminate\Validation\Rule;
use App\Models\User;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', Rule::unique('users', 'email'), 'string']
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => __('validation.name.required'),
            'name.max' => __('validation.name.max'),
            'name.min' => __('validation.name.min'),
            'email.required' => __('validation.email.required'),
        ];
    }

    public function storeUser(): void
    {
        $validated = $this->validate();
        $user = new User();
        // $user->fill($validated);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password = config('app.app_pw_default');
        $user->save();
        $user->syncRoles(['pegawai']);
        $this->reset();
        $this->dispatch('user-stored', name: $user->name);
        $this->dispatch('pg:eventRefresh-user-table-rlxt54-table');
    }
}; ?>

<section>
    <x-primary-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'open-create-user')">{{ __('Tambah User') }}
    </x-primary-button>
    <x-modal name="open-create-user" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Tambah User') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Masukkan informasi user pada form di bawah ini.') }}
                </p>
            </header>
            <form class="space-y-6" wire:submit="storeUser">
                <div>
                    <x-input-label for="name" :value="__('nama')" />
                    <x-text-input class="mt-1 block w-full" id="name" name="name" type="text" wire:model="name" required autofocus autocomplete="name" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div>
                    <x-input-label for="email" :value="__('email')" />
                    <x-text-input class="mt-1 block w-full" id="email" email="email" type="text" wire:model="email" required autofocus autocomplete="email" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="user-stored">
                        {{ __('Tersimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
