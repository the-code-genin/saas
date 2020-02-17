<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at', 'remember_token', 'userable_id', 'userable_type', 'password'];

    /**
     * Get array representation of the user.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $userable = $this->userable->toArray();
        $data = array_merge($userable, $data);

        return $data;
    }

    /**
     * Get the owner for this user instance.
     *
     * @return void
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }
}
