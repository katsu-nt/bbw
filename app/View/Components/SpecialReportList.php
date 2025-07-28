<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SpecialReportList extends Component
{
    public $title;
    public $reportList;
    public function __construct(?string $title, ?array $reportList)
    {
        $this->title = $title;
        $this->reportList = $reportList;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.special-report-list');
    }
}
