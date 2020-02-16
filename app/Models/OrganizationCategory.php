<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationCategory extends Model
{
    use SoftDeletes;

    /** @var string The table associated with the model. */
    protected $table = 'organization_categories';

    /**
     * Get the organizations that have this category.
     *
     * @return HasMany
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'category_id', 'id');
    }
}
