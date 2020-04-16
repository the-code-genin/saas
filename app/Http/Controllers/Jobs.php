<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Helpers\Api;
use App\Models\JobSkill;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

/**
 * Resource controller for jobs.
 */
class Jobs extends Controller
{
    /**
     * Get all jobs.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function index(Request $request): array
    {
        $results = Job::with(['skills', 'organization']);

        if (!empty($request->get('skill'))) { // If a skill filter is set
            $skill = $request->get('skill');
            $results = $results->whereHas('skills', function(Builder $skills) use ($skill) {
                $skills->where('name', 'LIKE', "%{$skill}%");
            }, '>', 0);
        }

        if (!empty($request->get('organization'))) { // If an organization filter is set
            $organizationName = $request->get('organization');
            $results = $results->whereHas('organization', function(Builder $user) use ($organizationName) {
                $user->whereHasMorph(
                    'userable',
                    Organization::class,
                    function (Builder $organization) use ($organizationName) {
                        $organization->where('name', 'LIKE', "%{$organizationName}%");
                    }
                );
            });
        }

        if (!empty($request->get('category'))) { // If a category filter is set
            $category = $request->get('category');
            $results = $results->where('category', 'LIKE', "%{$category}%");
        }

        if (!empty($request->get('page')) || !empty($request->get('perPage'))) { // If pagination is to be applied.
            $page = $request->get('page', 1);
            $perPage = $request->get('perPage', 10);

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

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Get a single job.
     *
     * @param int $id
     *
     * @return array
     */
    public function show(int $id): array
    {
        $job = Job::where('id', $id)->with(['skills', 'organization'])->first();

        if (is_null($job)) { // If the expert was not found
            return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
        }

        return [
            'success' => true,
            'payload' => [
                'data' => $job
            ]
        ];
    }

    /**
     * Create a job.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function create(Request $request): array
    {
        // Validate input.
        $validator = Validator::make($request->json()->all(), [
            'title' => 'required',
            'description' => 'required',
            'requirements' => 'required',
            'location' => 'required',
            'about_position' => 'required',
            'duties' => 'required',
            'category' => 'required|in:remote,weekend,weekday',
            'about_organization' => 'required',
            'skills' => 'required|array',
        ], [
            'category.in' => 'Invalid category.',
            'skills.array' => 'Invalid value for skills.'
        ]);

        if ($validator->fails()) { // Validation fails.
            return Api::generateErrorResponse(105, 'InvalidFormDataError', Api::getFirstValidationError($validator));
        }

        // Create the job.
        $job = new Job;
        $job->user_id = $request->user()->id;
        $job->title = $request->json('title');
        $job->description = $request->json('description');
        $job->requirements = $request->json('requirements');
        $job->location = $request->json('location');
        $job->about_position = $request->json('about_position');
        $job->duties = $request->json('duties');
        $job->category = $request->json('category');
        $job->about_organization = $request->json('about_organization');
        $job->save();

        // Add the job skills
        foreach ($request->json('skills') as $skillName) {
            $skill = new JobSkill;
            $skill->name = $skillName;
            $job->skills()->save($skill);
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
