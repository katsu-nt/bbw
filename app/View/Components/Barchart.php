<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Barchart extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $id;
    public $yearlyLabels;    
     public $yearlyDatasets;    
    public $quarterlyLabels;    
    public $quarterlyDatasets;
    public $legend;
    public $title;
    public $rightTitle;
    public $height;


    public function __construct(
    $id=null,
    $yearlyLabels = null,
    $yearlyDatasets = null,
    $quarterlyLabels = null,
    $quarterlyDatasets = null,
    $legend = null,
    $title = null,
    $rightTitle = null,
    $height = 120 )
    {
        $this->id = $id;
        $this->yearlyLabels = $yearlyLabels;
        $this->yearlyDatasets = $yearlyDatasets;
        $this->quarterlyLabels = $quarterlyLabels;
        $this->quarterlyDatasets = $quarterlyDatasets;
        $this->legend = $legend;
        $this->title = $title;
        $this->rightTitle = $rightTitle;
        $this->height = $height;
    
    
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.bar-chart');
    }
}