<?php

namespace App\View\Components;

use Illuminate\View\Component;

class StandardArticle extends Component
{
    public $articleDetail;
    public $articleTopReadList;
    public $hidecontentPremium;
    public $summarize;
    public $audio;
    public $canView;
    public $isPremiumContent;
    public $isSave;
    public $isLogin;

    public function __construct($articleDetail, $articleTopReadList = null, $canView = true, $hidecontentPremium = null, $isPremiumContent = false, $isSave = false, $audio = null, $summarize = null, $isLogin = false)
    {
        $this->articleDetail = $articleDetail;
        $this->articleTopReadList = $articleTopReadList;
        $this->canView = $canView;
        $this->hidecontentPremium = $hidecontentPremium;
        $this->isSave = $isSave;
        $this->isPremiumContent = $isPremiumContent;
        $this->audio = $audio;
        $this->summarize = $summarize;
        $this->isLogin = $isLogin;
    }

    public function render()
    {
        return view('components.standard-article');
    }
}
