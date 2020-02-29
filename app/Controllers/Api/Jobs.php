<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use App\Models\Job;
use App\Models\JobSkill;
use Cradle\Controller;
use Valitron\Validator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Resource controller for jobs.
 */
class Jobs extends Controller
{
    /**
     * Create a job.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param object $params
     *
     * @return array
     */
    protected function create(ServerRequestInterface $request, object $params): array
    {
        $input = $request->getAttribute('body');

        // Validate input.
        $validator = new Validator((array) $input);
        $validator->rule('required', [
            'title', 'description', 'requirements', 'location', 'about_position',
            'duties', 'category', 'about_organization', 'skills'
        ]);
        $validator->rule('in', 'category', ['remote', 'weekend', 'weekday']);
        $validator->rule('array', 'skills');
        if (!$validator->validate()) { // Validation fails.
            $errors = $validator->errors();
            return Api::generateErrorResponse(105, 'InvalidFormDataError', array_shift($errors)[0]);
        }

        // Create the job.
        $job = new Job;
        $job->user_id = $request->getAttribute('user')->id;
        $job->title = $input->title;
        $job->description = $input->description;
        $job->requirements = $input->requirements;
        $job->location = $input->location;
        $job->about_position = $input->about_position;
        $job->duties = $input->duties;
        $job->category = $input->category;
        $job->about_organization = $input->about_organization;
        $job->save();

        // Add the job skills
        foreach ($input->skills as $skillName) {
            $skill = new JobSkill;
            $skill->name = $skillName;
            $skill->job_id = $job->id;
            $skill->save();
        }

        // Response.
        return [
            'success' => true,
            'payload' => [
                'data' => $job->load('skills')
            ]
        ];
    }
}