<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardImgTitleTime extends Component
{
    public $image;
    public $title;
    public $datetimeUpdated;
    public $url;
    public function __construct(?string $image, ?string $title, ?string $datetimeUpdated, ?string $url)
    {
        $this->image = $image;
        $this->title = $title;
        $this->datetimeUpdated = $datetimeUpdated;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.card-img-title-time');
    }
}
