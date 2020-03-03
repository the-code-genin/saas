<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use App\Models\User;
use Cradle\Controller;
use App\Models\Student;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Resource controller for experts.
 * Experts are users
 */
class Experts extends Controller
{
    /**
     * Get all organization categories.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params): array
    {
        if (isset($params->id)) { // If a single expert is to be gotten
            $payload = [
                'data' => User::where('id', $params->id)
                    ->where('userable_type', Student::class)
                    ->first()
            ];
            if (is_null($payload['data'])) { // If the expert was not found
                return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
            }
        } else { // If a list of experts is to be gotten
            $results = User::select(['*']);

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
}
