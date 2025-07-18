<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="flex w-full justify-center font-bold uppercase text-xl">{{ config('app.name', '-') }}</h2>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login">
        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input class="block mt-1 w-full" id="email" name="email" type="email" wire:model="form.email"
                required autofocus autocomplete="username" placeholder="masukan email" />
            <x-input-error class="mt-2" :messages="$errors->get('form.email')" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input class="block mt-1 w-full" id="password" name="password" type="password"
                wire:model="form.password" required autocomplete="current-password" placeholder="masukan password" />

            <x-input-error class="mt-2" :messages="$errors->get('form.password')" />
        </div>

        <!-- Remember Me -->
        {{-- <div class="block mt-4">
            <label class="inline-flex items-center" for="remember">
                <input class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" id="remember"
                    name="remember" type="checkbox" wire:model="form.remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div> --}}

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                {{-- <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot your password?') }}
                </a> --}}
            @endif

            <x-primary-button class="ms-3">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>

</div>
