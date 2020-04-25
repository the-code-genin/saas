<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\StudentHire;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentHirePolicy
{
    use HandlesAuthorization;

    public function update(User $user, StudentHire $offer)
    {
        return $user->userable_type == Student::class && $user->id == $offer->student_id;
    }
}
