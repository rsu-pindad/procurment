<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatusExport implements FromView
{
    public function __construct(public array $data) {}

    public function view(): View
    {
        return view('exports.status', [
            'labels' => $this->data['labels'],
            'data' => $this->data['data'],
        ]);
    }
}
