<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HpsExport implements FromView
{
    protected array $labels;
    protected array $hps;
    protected array $hpsNego;

    public function __construct(array $chartData)
    {
        $this->labels = $chartData['labels'] ?? [];
        $this->hps = $chartData['data']['hps'] ?? [];
        $this->hpsNego = $chartData['data']['hps_nego'] ?? [];
    }

    public function view(): View
    {
        return view('exports.hps', [
            'labels' => $this->labels,
            'hps' => $this->hps,
            'hpsNego' => $this->hpsNego,
        ]);
    }
}
