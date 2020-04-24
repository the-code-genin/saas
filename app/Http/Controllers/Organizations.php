<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthenticationError;
use App\Models\Job;
use App\Helpers\Api;
use Illuminate\Http\Request;

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
     * @param Job $job
     *
     * @return array
     */
    public function jobApplications(Job $job): array
    {
        return [
            'success' => true,
            'payload' => [
                'data' => null
            ]
        ];
    }
}
