<?php

namespace Vanguard\Http\ViewComposers;

use Auth;
use Illuminate\View\View;

class SidebarComposer
{
    /**
     * Bind data to the view.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $chat = Auth::user()->chats()->first();
        $view->with(compact('chat'));
    }
}