<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Helpers\Api;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentProfileView;
use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Validator;

class Students extends Controller
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
        // Response
        return [
            'success' => true,
            'payload' => []
        ];
    }

    /**
     * update profile visits for a student.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return void
     */
    public function updateVisits(Request $request, int $id): array
    {
        $student = User::where('id', $id)
            ->where('userable_type', Student::class)
            ->first();

        if (is_null($student)) { // If the expert was not found
            return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
        }

        // Add a viewed record to the database.
        $view = new StudentProfileView();
        $view->organization_id = $request->user()->id;
        $view->student_id = $student->id;
        $view->save();

        // Response
        return [
            'success' => true,
            'payload' => []
        ];
    }

    /**
     * Apply for a job.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return array
     */
    public function applyForJob(Request $request, int $id): array
    {
        $job = Job::where('id', $id)->first();
        if (is_null($job)) { // If the job was not found
            return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
        }

        // Check if the user can apply for the job.
        if (!$request->user()->can('apply', $job)) {
            return Api::generateErrorResponse(401, 'AuthenticationError', 'You can not apply for the job.');
        }

        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'proposal' => 'required',
            'previous_experience' => 'required',
            'cover_letter' => 'required',
        ]);

        if ($validator->fails()) { // Validation fails.
            return Api::generateErrorResponse(105, 'InvalidFormDataError', Api::getFirstValidationError($validator));
        }

        // Apply for the job.
        $application = new JobApplication();
        $application->proposal = $request->json('proposal');
        $application->previous_experience = $request->json('previous_experience');
        $application->cover_letter = $request->json('cover_letter');
        $application->student_id = $request->user()->id;
        $job->applications()->save($application);


        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $application->refresh()->load(['job', 'job.organization', 'job.skills'])
            ]
        ];
    }
}
