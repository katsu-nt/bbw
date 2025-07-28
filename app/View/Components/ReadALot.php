<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ReadALot extends Component
{
    public $article;
    public $isNeedCheckPremium;

    public $isNeedNameChannel;
    public $isNeedTime;
    public function __construct(?array $article, ?bool $isNeedNameChannel = false, ?string $color = null, ?bool $isNeedTime = false, ?bool $isNeedCheckPremium = true)
    {
        //
        $this->article = $article;
        $this->isNeedNameChannel = $isNeedNameChannel;
        $this->isNeedTime = $isNeedTime;
        $this->isNeedCheckPremium = $isNeedCheckPremium;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.read-a-lot');
    }
}
