<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CarouselHaveArrow extends Component
{

    public $idList;
    public $itemList;
    public $aspect;
    public $textCustom;
    public $children;

    public $hasHashtag;
    public $type;
    public $thumbnail;
    public $hasTitle;
    public function __construct($idList, $itemList, $aspect = null, $textCustom = null, $children = null, $hasHashtag = false, $type = null, $thumbnail = '540x360', $hasTitle = null)
    {
        $this->idList = $idList;
        $this->itemList = $itemList;
        $this->aspect = $aspect;
        $this->textCustom = $textCustom;
        $this->children = $children;
        $this->hasHashtag = $hasHashtag;
        $this->type = $type;
        $this->thumbnail = $thumbnail;
        $this->hasTitle = $hasTitle;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.carousel-have-arrow');
    }
}
