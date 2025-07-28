<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardTimeTitle extends Component
{
    public $title;
    public $datetimeUpdated;
    public $url;
    public $isPremiumArticle;
    public $category;

    public function __construct(?string $title = null, ?string $datetimeUpdated = null, ?string $url = null, $isPremiumArticle = false, $category = null)
    {
        $this->title = $title;
        $this->datetimeUpdated = $datetimeUpdated;
        $this->url = $url;
        $this->isPremiumArticle = $isPremiumArticle;
        $this->category = $category;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.card-time-title');
    }
}
