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
use \App\Enums\JenisAjuan;
use App\Models\Admin\StatusAjuan;
use App\Models\Admin\Unit;

final class UserAjuanTable extends PowerGridComponent
{
    public string $tableName = 'user-ajuan-table-z2bm8x-table';
    public string $sortField = 'tanggal_update_terakhir';
    public string $sortDirection = 'desc';
    public bool $withSortStringNumber = true;

    public function boot(): void
    {
        config(['livewire-powergrid.filter' => 'inline']);
    }

    public function setUp(): array
    {
        // $this->showCheckBox();

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
        if (!auth()->user()->hasRole('pengadaan', true)) {
            // return Ajuan::query()->with(['unit','status_ajuan'])->where('users_id', auth()->id());
            return Ajuan::query()->with(['unit', 'status_ajuan'])->where('units_id', auth()->user()->unit_id);
        }

        return Ajuan::query()->with(['unit', 'status_ajuan']);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('units_id', fn ($model) => e($model->unit?->nama_unit))
            ->add('produk_ajuan')
            ->add('tanggal_ajuan_formatted', fn (Ajuan $model) => Carbon::parse($model->tanggal_ajuan)->format('d/m/Y'))
            ->add('hps', fn (Ajuan $model) => number_format($model->hps, 2, ',', '.'))
            // ->add('spesifikasi')
            // ->add('file_rab')
            // ->add('file_nota_dinas')
            // ->add('file_analisa_kajian')
            // ->add('jenis_ajuan', fn(Ajuan $model) => \App\Enums\JenisAjuan::from($model->jenis_ajuan)->labels())
            // ->add('jenis_ajuan', function ($model) {
            //     $val = JenisAjuan::tryFrom($model->jenis_ajuan) ?? JenisAjuan::DEFAULT;
            //     return $val->labels();
            // })
            // ->add('tanggal_update_terakhir_formatted', fn (Ajuan $model) => Carbon::parse($model->tanggal_update_terakhir)->format('d/m/Y H:i:s'))
            ->add('status_ajuans_id', fn ($model) => e($model->status_ajuan?->nama_status_ajuan));
    }

    public function columns(): array
    {
        return [
            Column::make('No', 'id')
                ->index(),
            Column::make('Unit', 'units_id')
                ->sortable()
                ->searchable(),
            Column::make('Nama Produk', 'produk_ajuan')
                ->searchable(),
            Column::make('Tanggal ajuan', 'tanggal_ajuan_formatted', 'tanggal_ajuan')
                ->sortable(),
            // Column::make('Jenis ajuan', 'jenis_ajuan')
            //     ->sortable()
            // ->searchable(),
            Column::make('Status', 'status_ajuans_id')
                ->sortable(),
            Column::action('#')
        ];
    }

    public function filters(): array
    {
        $data = Unit::find(auth()->user()->units_id);
        if (auth()->user()->hasRole('pengadaan')) {
            $data = Unit::all();
        }
        return [
            Filter::datepicker('tanggal_ajuan')
            ->params([
                // 'locale' => 'id_ID',
                'timezone' => 'Asia/Jakarta',
                'enableTime' => false,
                'enableSeconds' => false,
                'dateFormat' => 'd/m/Y'
            ]),
            // Filter::datetimepicker('tanggal_update_terakhir'),
            // Filter::inputText('units_id')->placeholder('filter unit'),
            Filter::select('units_id', 'units_id')
                ->dataSource($data)
                ->optionLabel('nama_unit')
                ->optionValue('id'),
            Filter::select('status_ajuans_id', 'status_ajuans_id')
                ->dataSource(StatusAjuan::all())
                ->optionLabel('nama_status_ajuan')
                ->optionValue('id'),
        ];
    }

    #[\Livewire\Attributes\On('hapus')]
    public function hapus($rowId)
    {
        // $this->dispatch('confirm-hapus', rowId:$rowId);
        $pesan = '';
        try {
            $ajuan = Ajuan::find($rowId);
            $ajuan->delete();
            if ($ajuan) {
                $pesan = 'Ajuan berhasil dihapus';
            }
            $this->dispatch('info-hapus', message: $pesan);
        } catch (\Throwable $th) {
            $this->dispatch('info-hapus', message: $th->getMessage());
        }
    }

    public function actions(Ajuan $row): array
    {
        $button = [
            Button::make('detail')
                ->slot('Detail')
                ->class('pg-btn-white')
                ->route('ajuan.detail', ['ajuan' => $row->id])
                ->navigate(),
            // ->target('_self')
        ];
        if (auth()->user()->hasRole('pengadaan', true)) {
            $button = array_merge(
                $button,
                [
                    Button::add('edit')
                        ->slot('Edit')
                        ->class('pg-btn-white')
                        ->id('edit')
                        // ->attributes([
                        // 'id' => 'edit-' . $row->id,
                        // 'class' => 'pg-btn-white text-blue-500'
                        // ])
                        ->route('ajuan.edit', ['ajuan' => $row->id])
                        ->navigate(),
                ],
                [
                    Button::add('hapus')
                        ->slot('Hapus')
                        // ->icon('default-eye', ['class' => 'font-bold'])
                        ->class('pg-btn-white')
                        ->id('hapus')
                        // ->attributes([
                        // 'id' => 'hapus-' . $row->id,
                        // 'class' => 'pg-btn-white text-red-500'
                        // ])
                        ->confirm('hapus ajuan?')
                        ->dispatch('hapus', ['rowId' => $row->id])
                ]
            );
        }
        return $button;
    }

    // public function actionRules($row): array
    // {
    //    return [
    //         Rule::button('detail')
    //             ->when(fn($row) => $row->units_id == auth()->user()->unit_id)
    //             ->hide(),
    //     ];
    // }

}
