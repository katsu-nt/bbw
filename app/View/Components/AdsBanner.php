<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdsBanner extends Component
{
    public $image;
    public $width;
    public $height;
    public $isLongAds;

    public function __construct(string $image, ?string $width, ?string $height, bool $isLongAds = false)
    {
        //
        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
        $this->isLongAds = $isLongAds;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.ads-banner');
    }
}
