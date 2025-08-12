<?php

use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Locked;

new class extends Component
{
    #[Locked]
    public $id;
    public $password;

    protected $listeners = [
        'editPassword' => 'openModalPassword'
    ];

    public function rules()
    {
        return [
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => ('validation.password.required'),
            'password.string' => ('validation.password.string'),
            'password.min' => ('validation.password.min')
        ];
    }

    public function updatePassword()
    {
        $validated = $this->validate();
        try {
            $user = User::findOrFail($this->id)
                ->update([
                    'password' => Hash::make($this->password),
                ]);
            if ($user) {
                $this->reset();
                $this->dispatch('password-edited');
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function openModalPassword($rowId)
    {
        // $this->js("alert('ok')");
        $this->id = $rowId;
        $this->dispatch('open-modal', 'open-user');
    }
}; ?>

<section>
    <x-modal name="open-user" :show="$errors->isNotEmpty()" focusable>
        <div class="p-4 space-y-4">
            <header>
                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Form Password') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('form ganti password.') }}
                </p>
            </header>
            <form class="space-y-6 space" wire:submit="updatePassword">
                <div>
                    <x-input-label for="password" :value="__('Ganti Password')" />
                    <x-text-input class="mt-1 block w-full" id="password" name="password" type="password" wire:model="password" required autofocus autocomplete="password" />
                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                </div>
                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>

                    <x-action-message class="me-3" on="password-edited">
                        {{ __('disimpan.') }}
                    </x-action-message>
                </div>
            </form>
        </div>
    </x-modal>
</section>
