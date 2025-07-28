<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ratioSection extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $ratioFundamental;
    public function __construct($ratioFundamental=null)
    {
        $this->ratioFundamental=$ratioFundamental;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ratio-section');
    }
}