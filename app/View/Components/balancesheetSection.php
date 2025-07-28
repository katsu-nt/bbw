<?php

namespace App\View\Components;

use Illuminate\View\Component;

class balancesheetSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */   public $balanceSheetFundamental;

    public function __construct($balanceSheetFundamental=null)
    {
        $this->balanceSheetFundamental = $balanceSheetFundamental;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.balancesheet-section');
    }
}