<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Job extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at'];

    /**
     * Get the organization that posted this job.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'id', 'organization_id');
    }

    /**
     * Get the skills that this job requires.
     *
     * @return HasMany
     */
    public function skills(): HasMany
    {
        return $this->hasMany(JobSkill::class, 'job_id', 'id');
    }
}
