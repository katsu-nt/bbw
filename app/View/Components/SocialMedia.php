<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SocialMedia extends Component
{
    public $direction;
    public $color;
    public $hasCircle;

    public $gap;
    public $shareLink;

    public function __construct(?string $color = "black", ?string $direction = "horizontal", ?bool $hasCircle = false, $gap = 'gap-x-3', $shareLink = null)
    {
        $this->direction = $direction ?? "horizontal";
        $this->color = $color ?? "black";
        $this->hasCircle = $hasCircle ?? false;
        $this->gap = $gap;
        $this->shareLink = $shareLink;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.social-media');
    }
}
