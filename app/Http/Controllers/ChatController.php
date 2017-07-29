<?php

namespace Vanguard\Http\Controllers;

use App;
use Auth;

class ChatController extends Controller
{
    /**
     * Shows the chat window to the user.
     *
     * @return mixed
     */
    public function index()
    {
        $url = explode(':', str_replace('http://', '', str_replace('https://', '', App::make('url')->to('/'))))[0];
        $userName = Auth::user()->username;

        return view('chat.index', compact("userName", "url"));
    }
}