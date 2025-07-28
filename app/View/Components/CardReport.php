<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardReport extends Component
{
    public $title;
    public $image;
    public $datetimeUpdated;
    public $description;
    public $url;
    public $isPremiumArticle;
    public function __construct(?string $title, ?string $image, ?string $datetimeUpdated, ?string $description, ?string $url, $isPremiumArticle = false)
    {
        $this->title = $title;
        $this->image = $image;
        $this->datetimeUpdated = $datetimeUpdated;
        $this->description = $description;
        $this->url = $url;
        $this->isPremiumArticle = $isPremiumArticle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.card-report');
    }
}
