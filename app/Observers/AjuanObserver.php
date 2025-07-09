<?php

namespace App\Observers;

use App\Models\Ajuan;
use App\Models\Admin\AjuanStatusAjuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjuanObserver
{
    /**
     * Handle the Ajuan "created" event.
     */
    public function created(Ajuan $ajuan): void
    {
        DB::table('ajuan_status_ajuan')->updateOrInsert(
            ['ajuan_id' => $ajuan->id],
            [
                'status_ajuan_id' => $ajuan->status_ajuans_id,
                'updated_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Handle the Ajuan "updated" event.
     */
    public function updated(Ajuan $ajuan): void
    {
        if ($ajuan->wasChanged('status_ajuans_id')) {
            AjuanStatusAjuan::updateOrCreate(
                ['ajuan_id' => $ajuan->id],
                [
                    'status_ajuan_id' => $ajuan->status_ajuans_id,
                    'updated_by' => Auth::id(),
                ]
            );
        }
    }

    /**
     * Handle the Ajuan "deleted" event.
     */
    public function deleted(Ajuan $ajuan): void
    {
        //
    }

    /**
     * Handle the Ajuan "restored" event.
     */
    public function restored(Ajuan $ajuan): void
    {
        //
    }

    /**
     * Handle the Ajuan "force deleted" event.
     */
    public function forceDeleted(Ajuan $ajuan): void
    {
        //
    }
}
