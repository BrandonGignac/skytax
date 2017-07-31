<?php

namespace Vanguard\Http\Requests\Chat;

use Vanguard\Http\Requests\Request;

class CreateChatRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:155|min:3'
        ];
    }
}
