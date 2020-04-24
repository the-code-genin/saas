<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model's applications.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return mixed
     */
    public function viewApplications(User $user, Job $job)
    {
        return $user->userable->jobs()->where('jobs.id', $job->id)->count() == 1;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return mixed
     */
    public function update(User $user, Job $job)
    {
        return $user->userable->jobs()->where('jobs.id', $job->id)->count() == 1;
    }

    /**
     * Determine whether the user can apply for the job.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return mixed
     */
    public function apply(User $user, Job $job)
    {
        return ($job->status == 'open' && $user->verified == true && $user->userable_type == Student::class);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return mixed
     */
    public function delete(User $user, Job $job)
    {
        return $user->userable->jobs()->where('id', $job->id)->count() == 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Job  $job
     * @return mixed
     */
    public function forceDelete(User $user, Job $job)
    {
        return $this->delete($user, $job);
    }
}
