<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SummarySection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $symbol;
    public $normalizedPerformance;
    public $latestFinancialMetric;
    public $allSymbols;
    public $latestOhlcv;
    public function __construct($symbol=null, $normalizedPerformance=null,$allSymbols=null, $latestOhlcv=null ,$latestFinancialMetric=null)
    {
        $this->symbol = $symbol;
        $this->normalizedPerformance = $normalizedPerformance;
        $this->allSymbols = $allSymbols;
        $this->latestFinancialMetric = $latestFinancialMetric;
        $this->latestOhlcv = $latestOhlcv;

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.summary-section');
    }
}