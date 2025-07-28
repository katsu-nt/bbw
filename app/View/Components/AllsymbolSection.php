<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AllsymbolSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $allSymbols;
    public $getSymbols;
    public function __construct($allSymbols=null, $getSymbols=null)
    {
        $this->allSymbols = $allSymbols;
        $this->getSymbols = $getSymbols;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.allsymbol-section');
    }
}