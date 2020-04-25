<?php
namespace App\Http\Controllers;

use App\Helpers\Api;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Resource controller for experts.
 * Experts are users.
 */
class Experts extends Controller
{
    /**
     * Get all organization categories.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function index(Request $request): array
    {
        $results = User::select(['*'])
            ->where('userable_type', Student::class)
            ->with('userable.skills');

        if (!empty($request->get('skill'))) { // If a skill filter is set
            $skill = $request->get('skill');
            $results = $results->whereHasMorph('userable', [Student::class], function(Builder $student) use ($skill) {
                $student->whereHas('skills', function(Builder $skills) use ($skill) {
                    $skills->where('name', 'LIKE', "%{$skill}%");
                });
            }, '>', 0);
        }

        if (!empty($request->get('availability'))) { // If an availability filter is set
            $availability = $request->get('availability');
            $results = $results->whereHasMorph('userable', [Student::class], function(Builder $student) use ($availability) {
                $student->where('availability', 'LIKE', "%{$availability}%");
            });
        }

        if (!empty($request->get('range'))) { // If hourly range filter is set
            $range = explode(',', $request->get('range'), 2);
            $valid = true;
            foreach ($range as $value) {
                if (!preg_match('/\d+/', $value)) $valid = false;
            }

            if ($valid) {
                $results = $results->whereHasMorph('userable', [Student::class], function(Builder $student) use ($range) {
                    $student->where('hourly_rate', '>=', $range[0]);
                    if (count($range) > 1) $student->where('hourly_rate', '<=', $range[1]);
                });
            }
        }

        $payload = Api::getPayload($request, $results);

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Get a single expert.
     *
     * @param int $id
     *
     * @return array
     */
    public function show(User $expert): array
    {
        if ($expert->userable_type != Student::class) {
            throw new NotFoundResourceException('The resource you requested for was not found.', 404);
        }

        return [
            'success' => true,
            'payload' => [
                'data' => $expert->load('userable.skills', 'userable.hires.organization')
            ]
        ];
    }
}
