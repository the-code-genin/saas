<?php
namespace App\Http\Controllers;

use App\Helpers\Api;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $results = User::select(['*'])->where('userable_type', Student::class);

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
     * Get a single expert.
     *
     * @param int $id
     *
     * @return array
     */
    public function show(int $id): array
    {
        $expert = User::where('id', $id)
            ->where('userable_type', Student::class)
            ->first();

        if (is_null($expert)) { // If the expert was not found
            return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
        }

        return [
            'success' => true,
            'payload' => [
                'data' => $expert
            ]
        ];
    }
}
