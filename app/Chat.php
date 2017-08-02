<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug'];

    /**
     * Sets up the one-to-many association with the Message model.
     *
     * @return HasMany
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Sets up the many-to-many association with the User model.
     *
     *  @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
