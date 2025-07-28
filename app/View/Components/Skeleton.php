<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Skeleton extends Component
{
    public $containerStyle;

    public function __construct($containerStyle = '')
    {
        $this->containerStyle = $containerStyle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.skeleton');
    }
}
