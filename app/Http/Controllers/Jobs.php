<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Helpers\Api;
use App\Models\JobSkill;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exceptions\InvalidFormDataError;
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

        if (!empty($request->get('status'))) {
            $results = $results->where('status', $request->get('status'));
        }

        $payload = Api::getPayload($request, $results);

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Get a single job.
     *
     * @param Job $job
     *
     * @return array
     */
    public function show(Job $job): array
    {
        return [
            'success' => true,
            'payload' => [
                'data' => $job->load(['skills', 'organization'])
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
            'requirement' => 'required',
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
            throw new InvalidFormDataError(Api::getFirstValidationError($validator));
        }

        // Create the job.
        $job = new Job;
        $job->user_id = $request->user()->id;
        $job->title = $request->json('title');
        $job->description = $request->json('description');
        $job->requirement = $request->json('requirement');
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
