<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Organization extends Model
{
    use SoftDeletes;

    /**
     * Get the category for an organization.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(OrganizationCategory::class, 'id', 'category_id');
    }

    /**
     * Get the user instance.
     *
     * @return MorphOne
     */
    public function userable(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }
}
