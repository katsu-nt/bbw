<?php

namespace App\View\Components;

use Illuminate\View\Component;

class IncomeStatement extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $incomeStatementQuarterlyStructure;
    public $incomeStatementYearlyStructure;

  
    public function __construct($incomeStatementQuarterlyStructure=null,$incomeStatementYearlyStructure=null)
    {
        $this->incomeStatementQuarterlyStructure = $incomeStatementQuarterlyStructure;
        $this->incomeStatementYearlyStructure = $incomeStatementYearlyStructure;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.income-statement');
    }
}