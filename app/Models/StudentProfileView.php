<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfileView extends Model
{
    use SoftDeletes;

    /** @var array */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /** @var array */
    protected $hidden = ['updated_at', 'deleted_at', 'student_id', 'organization_id', 'created_at'];

    /**
     * Get the view student object.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    /**
     * Get the organization object that viewed the student object.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organization_id', 'id');
    }
}
