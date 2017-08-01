<?php

namespace Vanguard\Http\ViewComposers;

use Auth;
use Illuminate\View\View;
use Vanguard\Chat;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $chat = Chat::where('user_id',  Auth::user()->id)->first();
        $view->with(compact('chat'));
    }
}