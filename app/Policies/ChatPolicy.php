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
        foreach ($user->chats as $userChat) {
            if ($userChat->id === $chat->id) {
                return true;
            }
        }

        return false;
    }
}