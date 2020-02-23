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
    protected $appends = ['user_type', 'verified'];

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at', 'remember_token', 'userable_id', 'userable_type', 'password', 'userable'];

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
        if ($value == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Set the verified attribute.
     *
     * @param bool $value
     *
     * @return void
     */
    public function setVerifiedAttribute(bool $value): void
    {
        if ($value) {
            $this->attributes['verified'] = 'true';
        }
        $this->attributes['verified'] = 'false';
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
}
