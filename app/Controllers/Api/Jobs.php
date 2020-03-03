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
     * Get all jobs.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params): array
    {
        if (isset($params->id)) { // If a single job is to be gotten
            $payload = [
                'data' => Job::where('id', $params->id)->with(['skills', 'organization'])->first()
            ];
            if (is_null($payload['data'])) { // If the expert was not found
                return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
            }
        } else { // If a list of experts is to be gotten
            $results = Job::with(['skills', 'organization']);

            
            if (isset($request->getQueryParams()['page']) || isset($request->getQueryParams()['perPage'])) { // If pagination is to be applied.
                $page = isset($request->getQueryParams()['page']) ? $request->getQueryParams()['page'] : 1;
                $perPage = isset($request->getQueryParams()['perPage']) ? $request->getQueryParams()['perPage'] : 10;

                /** @var Paginator */
                $results = $results->paginate($perPage, ['*'], 'results', $page);

                $payload = [
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                    'current_page' => $results->currentPage(),
                    'prev_page' => ($results->currentPage() > 1) ? $results->lastPage() : null,
                    'next_page' => $results->hasMorePages() ? ($results->currentPage() + 1) : null,
                    'from' => $results->firstItem(),
                    'to' => $results->lastItem(),
                    'data' => $results->items(),
                ];
            } else { // If all are to be gotten at once.
                $payload = [
                    'data' => $results->get(),
                    'total' => $results->count(),
                ];
            }
        }

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Create a job.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param object $paramsselect(['*'])
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