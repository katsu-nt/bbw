<?php

namespace App\View\Components;

use Illuminate\View\Component;

class incomestatementSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $incomeStatementFundamental;

    public function __construct($incomeStatementFundamental=null)
    {
        $this -> incomeStatementFundamental = $incomeStatementFundamental;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.incomestatement-section');
    }
}