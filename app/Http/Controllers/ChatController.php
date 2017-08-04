<?php

namespace Vanguard\Http\Controllers;

use App;
use Auth;
use Cookie;
use Input;
use Vanguard\Chat;
use Vanguard\Http\Requests\Chat\ChatRequest;
use Vanguard\Repositories\User\UserRepository;

class ChatController extends Controller
{

    /**
     * ChatController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:chats.manage', ['except' => ['show']]);
    }

    /**
     * Displays the list of all chats.
     *
     * @return mixed
     */
    public function index()
    {
        $perPage = 6;
        $chats = Chat::with('users')->latest()->paginate($perPage);

        return view('chat.index', compact("chats"));
    }

    /**
     * Displays the form for creating a new chat.
     *
     * @param UserRepository $users
     *
     * @return mixed
     */
    public function create(UserRepository $users)
    {
        $perPage = 30;
        $users = $users->paginate($perPage, Input::get('search'));

        return view('chat.add', compact('users'));
    }

    /**
     * Displays the specified chat for a client.
     *
     * @param $slug
     *
     * @return mixed
     */
    public function show($slug)
    {
        $chat = Chat::where('slug', $slug)->with('messages')->first();
        $this->authorize('display', $chat);
        $user = Auth::user();
        $chats = $user->chats()->with('messages')->get();
        $url = explode(':', str_replace('http://', '', str_replace('https://', '', App::make('url')->to('/'))))[0];

        Cookie::queue('chat_id', $chat->id, 2628000);
        Cookie::queue('user_id', $user->id, 2628000);

        return view('chat.clients.show', compact("user", "chat", "chats", "url"));
    }

    /**
     * Stores a newly created chat.
     *
     * @param ChatRequest $request
     *
     * @return mixed
     */
    public function store(ChatRequest $request)
    {
        $chat = Chat::create([
            'title' => $request->get('title'),
            'slug' => $this->generateRandomString(),
        ]);

        $chat->users()->sync(request('users'));

        return redirect()->route('chat.list')
            ->withSuccess('New chat has been created.');
    }

    /**
     * Displays the form for editing specific chat.
     *
     * @param Chat $chat
     * @param UserRepository $users
     *
     * @return mixed
     */
    public function edit(Chat $chat, UserRepository $users)
    {
        $perPage = 30;
        $users = $users->paginate($perPage, Input::get('search'));

        return view('chat.edit', compact('chat', 'users'));
    }

    /**
     * Update specified chat.
     *
     * @param Chat $chat
     * @param ChatRequest $request
     *
     * @return mixed
     */
    public function update(Chat $chat, ChatRequest $request)
    {
        $chat->update([
            'title' => $request->get('title')
        ]);

        $chat->users()->sync(request('users'));

        return redirect()->route('chat.list')
            ->withSuccess('The chat has been updated.');
    }

    /**
     * Creates randomly generated url for chat.
     *
     * @param int $length
     *
     * @return mixed
     */
    private function generateRandomString($length = 20)
    {
        $string = str_random($length);
        $string = strtolower($string);

        if (Chat::where('slug', $string)->exists()) {
            return $this->generateSecureRandomString();
        };

        return $string;
    }
}
