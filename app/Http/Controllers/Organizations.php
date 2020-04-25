<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Helpers\Api;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use App\Exceptions\AuthenticationError;

class Organizations extends Controller
{
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
        return [];
    }
}
