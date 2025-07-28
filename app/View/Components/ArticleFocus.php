<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ArticleFocus extends Component
{
    public $article;
    public $heightImage;
    public $widthImage;
    public $aspectRadio;
    public $isLoading;
    public $isPremiumArticle;

    public function __construct($article, ?string $heightImage = null, ?string $widthImage = null, ?string $aspectRadio = null, $isLoading = false, $isPremiumArticle = false)
    {
        $this->article = $article;
        $this->heightImage = $heightImage;
        $this->widthImage = $widthImage;
        $this->aspectRadio = $aspectRadio;
        $this->isLoading = $isLoading;
        $this->isPremiumArticle = $isPremiumArticle;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.article-focus');
    }
}
