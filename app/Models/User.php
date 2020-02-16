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
