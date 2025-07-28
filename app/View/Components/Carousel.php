<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Carousel extends Component
{
    public $articleList;
    public $matterPrintedList;
    public $idList;
    public $isLoadingArticleList;
    public $isLoadingMatterPrintedList;

    public function __construct(?array $articleList = null, ?array $matterPrintedList = null, ?array $idList = null, $isLoadingArticleList = false, $isLoadingMatterPrintedList = false)
    {
        $this->articleList = $articleList;
        $this->matterPrintedList = $matterPrintedList;
        $this->idList = $idList;
        $this->isLoadingArticleList = $isLoadingArticleList;
        $this->isLoadingMatterPrintedList = $isLoadingMatterPrintedList;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.carousel');
    }
}
