<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TopicArticleList extends Component
{
    public $title;
    public $articleList;

    public function __construct(?string $title, ?array $articleList)
    {
        //
        $this->title = $title;
        $this->articleList = $articleList;
    }
    public function render()
    {
        return view('components.topic-article-list');
    }
}
