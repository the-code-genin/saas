<?php

namespace App\Models;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganizationCategory extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var string The table associated with the model. */
    protected $table = 'organization_categories';

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at'];

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
