<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Helpers\Api;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Exceptions\AuthenticationError;
use App\Exceptions\InvalidFormDataError;
use App\Models\StudentHire;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use App\Notifications\StudentApplicationUpdated;
use Illuminate\Support\Facades\Log;

class Organizations extends Controller
{
    /**
     * Get profile overview for a student.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function profileOverview(Request $request): array
    {
        // Statistical data.
        $noJobsPosted = $request->user()->userable->jobs()->count();

        $noJobsCompleted = Job::where('jobs.user_id', $request->user()->id)
            ->where('job_applications.status', 'accepted')
            ->join('job_applications', 'job_applications.job_id', 'jobs.id')
            ->join('users', 'job_applications.student_id', 'users.id')
            ->count();

        $hires = StudentHire::where('student_hires.organization_id', $request->user()->id)
            ->where('status', 'accepted')
            ->count();

        $noStudentsHired = $hires + $noJobsCompleted;

        // Response
        return [
            'success' => true,
            'payload' => [
                'data' => [
                    'jobs_posted' => $noJobsPosted,
                    'students_hired' => $noStudentsHired,
                    'jobs_completed' => $noJobsCompleted,
                ]
            ]
        ];
    }

    /**
     * Get all jobs for the organization.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function jobs(Request $request): array
    {
        $results = $request->user()->userable->jobs()->with('skills');

        $payload = Api::getPayload($request, $results);

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Close a job.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Job $job
     *
     * @return array
     */
    public function closeJob(Request $request, Job $job): array
    {
        if (!$request->user()->can('update', $job)) {
            throw new AuthenticationError('You can not close the job.');
        } else if ($job->status == 'closed') {
            throw new AuthenticationError("This job has already been closed.");
        }

        // Close the job.
        $job->status = 'closed';
        $job->save();

        return [
            'success' => true,
            'payload' => [
                'data' => $job->refresh()->id
            ]
        ];
    }

    /**
     * Get all applications for a job.
     *
     * @param Request $request
     * @param Job $job
     *
     * @return array
     */
    public function jobApplications(Request $request, Job $job): array
    {
        if (!$request->user()->can('viewApplications', $job)) {
            throw new AuthenticationError('User can not view applications for this job.');
        }

        $results = $job->applications()->with(['student']);
        $payload = Api::getPayload($request, $results);

        return [
            'success' => true,
            'payload' => [
                'data' => $payload
            ]
        ];
    }

    /**
     * Update a job application's status.
     *
     * @param Request $request
     * @param \App\Models\JobApplication $jobApplication
     *
     * @return array
     */
    public function updateJobApplication(Request $request, JobApplication $jobApplication): array
    {
        if (!$request->user()->can('viewApplications', $jobApplication->job)) {
            throw new AuthenticationError('User can not view applications for this job.');
        } else if ($jobApplication->status != 'pending') {
            throw new AuthenticationError("This job application has already been {$jobApplication->status}.");
        }

        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'status' => 'required|in:accepted,rejected',
        ], [
            'status.in' => 'Invalid value for status.',
        ]);

        if ($validator->fails()) { // Validation fails.
            throw new InvalidFormDataError(Api::getFirstValidationError($validator));
        }

        // Update the job application status.
        $jobApplication->status = $request->json('status');
        $jobApplication->save();

        // Send notifications to the organization and student.
        Notification::send([$request->user(), $jobApplication->student], new StudentApplicationUpdated($jobApplication));

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $jobApplication->refresh()->load(['student', 'job', 'job.skills'])
            ]
        ];
    }
}
