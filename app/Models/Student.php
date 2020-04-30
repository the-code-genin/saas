<?php

namespace App\Models;

use App\Models\User;
use App\Models\StudentSkill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $casts = [
        'hourly_rate' => 'float',
    ];

    protected $touches = ['user'];

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var array */
    protected $hidden = ['id', 'updated_at', 'deleted_at'];


    /**
     * Get the student hourly rate
     *
     * @param mixed $value
     *
     * @return float
     */
    public function getHourlyRateAttribute($value): float
    {
        return (float) $this->attributes['hourly_rate'];
    }

    /**
     * Get the verified attribute.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function getAvailableForJobAttribute($value): bool
    {
        if ($value == 'true') {
            return true;
        }

        return false;
    }

    /**
     * Get the user instance.
     *
     * @return MorphOne
     */
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    /**
     * Get the views for the student profile.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function views(): HasManyThrough
    {
        return $this->hasManyThrough(StudentProfileView::class, User::class, 'userable_id', 'student_id');
    }

    /**
     * Get the student's submitted applications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function applications(): HasManyThrough
    {
        return $this->hasManyThrough(JobApplication::class, User::class, 'userable_id', 'student_id');
    }

    /**
     * Get the student's job offers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function hires(): HasManyThrough
    {
        return $this->hasManyThrough(StudentHire::class, User::class, 'userable_id', 'student_id');
    }
}
