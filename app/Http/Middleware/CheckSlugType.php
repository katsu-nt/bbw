<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSlugType
{
    public function handle(Request $request, Closure $next)
    {
        $slug = $request->route('slug');

        if (preg_match('/-\d+\.html$/', $slug)) {
            return redirect()->route('article.show', ['slugPublisher' => $slug]);
        } else {
            return redirect()->route('category.show', ['slug' => $slug]);
        }
    }
}

