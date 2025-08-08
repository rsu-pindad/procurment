<?php

use Livewire\Volt\Component;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use App\Imports\AjuanImport;
use Livewire\Attributes\Validate;

new class extends Component
{
    use WithFileUploads;

    #[Validate('required|file|mimes:csv')]
    public $file;

    public function import()
    {
        $this->validate();
        Excel::import(new AjuanImport(), $this->file);
        $this->dispatch('modal-stored', name: 'import');
    }
}; ?>

<section class="p-2">
    <form>
        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <!-- Input File -->
            <div class="w-full md:w-2/3">
                <x-input-label for="file" :value="__('hanya format file bertipe csv')" />
                <x-file-input class="mt-1 block w-full" id="file" wire:model="file" accept=".csv" autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('file')" />
            </div>

            <!-- Tombol Import -->
            <div class="w-full md:w-auto">
                <x-primary-button class="w-full md:w-auto" type="button" wire:click="import">
                    {{ __('Import') }}
                </x-primary-button>
                <x-action-message class="mt-2 md:mt-1" on="importStore">
                    {{ __('import selesai.') }}
                </x-action-message>
            </div>
        </div>
    </form>
</section>
