<?php

namespace App\Livewire;

use App\Models\Ajuan;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class UserAjuanTable extends PowerGridComponent
{
    public string $tableName = 'user-ajuan-table-z2bm8x-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Ajuan::query()->with(['unit','status_ajuan'])->where('users_id', auth()->id());
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('units_id', fn($model) => e($model->unit?->nama_unit))
            ->add('tanggal_ajuan_formatted', fn (Ajuan $model) => Carbon::parse($model->tanggal_ajuan)->format('d/m/Y'))
            ->add('hps', fn (Ajuan $model) => number_format($model->hps, 2, ',', '.'))
            ->add('spesifikasi')
            ->add('file_rab')
            ->add('file_nota_dinas')
            ->add('file_analisa_kajian')
            ->add('jenis_ajuan', fn(Ajuan $model) => \App\Enums\JenisAjuan::from($model->jenis_ajuan)->labels())
            ->add('tanggal_update_terakhir_formatted', fn (Ajuan $model) => Carbon::parse($model->tanggal_update_terakhir)->format('d/m/Y H:i:s'))
            ->add('status_ajuans_id', fn($model) => e($model->status_ajuan?->nama_status_ajuan));
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'id')
            ->index(),
            Column::make('Unit', 'units_id'),
            Column::make('Tanggal ajuan', 'tanggal_ajuan_formatted', 'tanggal_ajuan')
                ->sortable(),
            Column::make('Jenis ajuan', 'jenis_ajuan')
                ->sortable()
                ->searchable(),
            Column::make('Status', 'status_ajuans_id'),

            Column::action('#')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('tanggal_ajuan'),
            Filter::datetimepicker('tanggal_update_terakhir'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions(Ajuan $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
    }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
