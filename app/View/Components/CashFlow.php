<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CashFlow extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $cashflowQuarterlyStructure;
    public $cashflowYearlyStructure;
    public function __construct($cashflowQuarterlyStructure=null,$cashflowYearlyStructure=null)
    {
        $this->cashflowQuarterlyStructure = $cashflowQuarterlyStructure;
        $this->cashflowYearlyStructure = $cashflowYearlyStructure;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view(view: 'components.cash-flow');
    }
}