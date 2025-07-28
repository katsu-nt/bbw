<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TopicList extends Component
{
    public $title;
    public $itemList;
    public $isCategory;

    public $oneColumn;
    public $gap;
    public $titleSize;
    public $isOpenNewTab;
    public $icon;

    public function __construct(
        ?string $title = null,
        array $itemList = [],
        ?bool $isCategory = true,
        $oneColumn = false,
        $gap = 'gap-y-1',
        $titleSize = 'text-sm',
        ?bool $isOpenNewTab = false,
        $icon = null
    ) {
        $this->title = $title;
        $this->itemList = $itemList;
        $this->isCategory = $isCategory;
        $this->oneColumn = $oneColumn;
        $this->gap = $gap;
        $this->titleSize = $titleSize;
        $this->isOpenNewTab = $isOpenNewTab;
        $this->icon = $icon;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.topic-list');
    }
}
