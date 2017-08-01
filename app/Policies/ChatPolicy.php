<?php

namespace Vanguard\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Vanguard\Chat;
use Vanguard\User;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determines whether the user can access the chat.
     *
     * @param User $user
     * @param Chat $chat
     *
     * @return bool
     */
    public function display(User $user, Chat $chat)
    {
        return $chat->user_id === $user->id;
    }
}