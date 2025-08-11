<?php

use Livewire\Volt\Component;
use App\Models\User;
use Laratrust\Models\Role;
use Livewire\Attributes\Locked;

new class extends Component
{
    public ?int $selectedRole;

    #[Locked]
    public $id;

    public function mount($user)
    {
        $this->id = $user;
        $this->lastUserRole = User::find($user)->roles->first();
        $this->selectedRole = $this->lastUserRole->id;
        // dd($this->selectedRole);
        // dd($this->lastUserRole);
    }

    public function roles()
    {
        return Role::whereNot('name','admin')->whereNot('name','pengadaan')->get();
    }

    public function updatedSelectedRole($value): void
    {
        // dd($value);
        $user = User::find($this->id);
        $user->roles()->sync($value);
        $this->dispatch('updated-user-role');
    }

    public function goBack(): void
    {
        $this->redirect('/manajemen/user', navigate: true);
    }
}; ?>

<section>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Manajemen User Role') }}
        </h2>
    </x-slot>
    <div class="py-5 sm:py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 bg-white shadow-md sm:rounded-lg">
                <!-- Header -->
                <div class="px-4 py-5 sm:flex sm:items-center sm:justify-between">
                    <x-section-header title="Manajemen User Role">
                        Berikut adalah role user yang tersedia dengan detail informasi lengkap.
                    </x-section-header>
                    <button class="mt-4 sm:mt-0 px-4 py-2 bg-blue-100 text-sm text-blue-700 rounded-md hover:bg-blue-200 transition" wire:click="goBack">
                        @svg('heroicon-s-arrow-left', 'w-5 h-5 inline-flex mx-2')
                    </button>
                </div>

                <div class="p-4 space-y-4">
                    <form wire:submit="store">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                            @foreach($this->roles() as $role)
                            <div>
                                <x-toggle-switch id="role-{{ $role->id }}" type="radio" name="selectedRole" value="{{ $role->id }}" label="{{ $role->name }}" wire:model.lazy="selectedRole" />
                            </div>
                            @endforeach
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
