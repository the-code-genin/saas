<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use SoftDeletes;

    /**
     * Get the user instance.
     *
     * @return MorphOne
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
