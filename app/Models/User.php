<?php

namespace App\Models;

use App\Models\UserApiToken;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var array */
    protected $appends = ['user_type', 'verified'];

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at', 'remember_token', 'password', 'userable_id', 'userable', 'userable_type'];

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
     * Get the verified attribute.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function getVerifiedAttribute($value): bool
    {
        if ($this->status == 'active') {
            return true;
        }

        return false;
    }

    /**
     * Get the user type based on the userable property.
     *
     * @param mixed $value
     * @return string
     */
    public function getUserTypeAttribute($value): string
    {
        switch ($this->attributes['userable_type']) {
            case Student::class:
                $type = 'student';
            break;

            case Organization::class:
                $type = 'organization';
            break;

            default:
                $type = 'unknown';
            break;
        }
        return $type;
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

    public function tokens(): HasMany
    {
        return $this->hasMany(UserApiToken::class, 'user_id', 'id');
    }

    public function verificationTokens(): HasMany
    {
        return $this->hasMany(UserVerificationToken::class, 'user_id', 'id');
    }
}
