<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardImgTitle extends Component
{
    public $image;
    public $title;
    public $nameChannel;
    public $fontSize;
    public $type;
    public $isReverse;
    public $url;
    public $breakline;
    public $heightImage;
    public $widthImage;

    public $hasNameChannel;
    public $isPremiumArticle;
    public $isLoading;

    public function __construct(
        ?string $image = null,
        ?string $title = null,
        ?string $nameChannel = null,
        ?string $fontSize = 'text-base',
        ?string $type = 'outside',
        ?bool $isReverse = false,
        ?string $url = '',
        ?bool $breakline = false,
        ?string $heightImage = 'h-40',
        ?string $widthImage = 'w-40',
        ?bool $hasNameChannel = false,
        ?bool $isPremiumArticle = false,
        ?bool $isLoading = false
    ) {
        $this->image = $image;
        $this->title = $title;
        $this->nameChannel = $nameChannel;
        $this->fontSize = $fontSize;
        $this->type = $type;
        $this->isReverse = $isReverse;
        $this->url = $url;
        $this->breakline = $breakline;
        $this->heightImage = $heightImage;
        $this->widthImage = $widthImage;
        $this->hasNameChannel = $hasNameChannel;
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
        return view('components.card-img-title');
    }
}
