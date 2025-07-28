<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Opinion extends Component
{
    public $articles;
    public $idList;

    public function __construct(?array $articles, $idList)
    {
        $this->articles = $articles;
        $this->idList = $idList;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.opinion');
    }
}
