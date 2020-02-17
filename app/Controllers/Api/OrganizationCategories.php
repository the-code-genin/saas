<?php
namespace App\Controllers\Api;

use App\Helpers\Api;
use Cradle\Controller;
use App\Models\OrganizationCategory;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Pagination\Paginator;

class OrganizationCategories extends Controller
{
    /**
     * Get all organization categories.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params): array
    {
        if (isset($params->id)) { // If a single category is to be gotten
            $payload = [
                'data' => OrganizationCategory::select(['id', 'name'])->where('id', $params->id)->first()
            ];
            if (is_null($payload['data'])) { // If the category was not found
                return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
            }
        } else { // If a list of categories is to be gotten
            if (isset($request->getQueryParams()['page']) || isset($request->getQueryParams()['perPage'])) { // If pagination is to be applied.
                $page = $request->getQueryParams()['page'];
                $perPage = isset($request->getQueryParams()['perPage']) ? $request->getQueryParams()['perPage'] : 10;
                
                /** @var Paginator */
                $results = OrganizationCategory::paginate($perPage, ['id', 'name'], 'results', $page);
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
                $results = OrganizationCategory::select(['id', 'name']);
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
     * Create a new category.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function create(ServerRequestInterface $request, object $params): array
    {
        $categories = OrganizationCategory::select(['id', 'name'])->get();

        return [
            'success' => true,
            'payload' => $categories
        ];
    }

    /**
     * Update a category.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function update(ServerRequestInterface $request, object $params): array
    {
        $categories = OrganizationCategory::select(['id', 'name'])->get();

        return [
            'success' => true,
            'payload' => $categories
        ];
    }

    /**
     * Delete a category.
     *
     * @param ServerRequestInterface $request
     * @param object $params
     * @return array
     */
    protected function destroy(ServerRequestInterface $request, object $params): array
    {
        $categories = OrganizationCategory::select(['id', 'name'])->get();

        return [
            'success' => true,
            'payload' => $categories
        ];
    }
}
