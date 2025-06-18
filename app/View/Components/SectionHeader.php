<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SectionHeader extends Component
{

    public function __construct(public string $title) {}

    public function render()
    {
        return view('components.section-header');
    }
}
