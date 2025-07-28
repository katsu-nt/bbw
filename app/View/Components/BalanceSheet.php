<?php

namespace App\View\Components;

use Illuminate\View\Component;

class BalanceSheet extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $balanceQuarterlyStructure;
    public $balanceYearlyStructure;    
    public function __construct($balanceQuarterlyStructure=null,$balanceYearlyStructure=null)
    {
        $this->balanceQuarterlyStructure = $balanceQuarterlyStructure;
        $this->balanceYearlyStructure = $balanceYearlyStructure;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.balance-sheet');
    }
}