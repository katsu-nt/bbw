<?php

namespace App\View\Components;

use Illuminate\View\Component;

class LongFormUploadArticle extends Component
{
    public $articleDetail;
    public $canView;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($articleDetail, $canView)
    {
        //
        $this->articleDetail = $articleDetail;
        $this->canView = $canView;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.long-form-upload-article');
    }
}
