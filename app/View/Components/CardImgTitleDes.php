<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardImgTitleDes extends Component
{
    public $image;
    public $title;
    public $description;
    public $url;
    public $heightImage;
    public $isPremiumArticle;
    public $isLoading;

    public function __construct(?string $image, ?string $title, ?string $description, ?string $url, $heightImage, $isPremiumArticle = false, $isLoading = false)
    {
        $this->image = $image;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->heightImage = $heightImage;
        $this->isPremiumArticle = $isPremiumArticle;
        $this->isLoading = $isLoading;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.card-img-title-des');
    }
}
