<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LongFormArticle extends Component
{
    public $articleDetail;
    public $summarize;
    public $audio;
    public $hidecontentPremium;
    public $canView;
    public $isSave;
    public $isLogin;

    public function __construct($articleDetail, $hidecontentPremium, $canView, $isSave = null, $summarize = null, $audio = null, $isLogin = false)
    {
        $this->articleDetail = $articleDetail;
        $this->hidecontentPremium = $hidecontentPremium;
        $this->canView = $canView;
        $this->isSave = $isSave;
        $this->summarize = $summarize;
        $this->audio = $audio;
        $this->isLogin = $isLogin;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.long-form-article');
    }
}
