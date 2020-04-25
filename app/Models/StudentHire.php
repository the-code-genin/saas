<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHire extends Model
{
    /** @var array */
    protected $hidden = ['updated_at', 'student_id', 'organization_id'];

    /**
     * Get the student that is being hired.
     *
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    /**
     * Get the organization that is hiring.
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organization_id', 'id');
    }
}
