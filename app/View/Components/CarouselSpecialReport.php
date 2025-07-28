<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CarouselSpecialReport extends Component
{
    public $reportList;
    public $idList;

    public function __construct(?array $reportList, $idList)
    {
        $this->reportList = $reportList;
        $this->idList = $idList;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.carousel-special-report');
    }
}
