<?php
namespace App\Http\Controllers;

use App\Helpers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrganizationCategory;
use Illuminate\Pagination\Paginator;

/**
 * Resource controller for organization categories.
 */
class OrganizationCategories extends Controller
{
    /**
     * Get all organization categories.
     *
     * @return array
     */
    public function index(Request $request): array
    {
        if (!empty($request->get('page', null)) || !empty($request->get('perPage'))) { // If pagination is to be applied.
            $page = $request->get('page', 1);
            $perPage = $request->get('perPage', 10);

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

        return [
            'success' => true,
            'payload' => $payload
        ];
    }

    /**
     * Show a single organization category.
     *
     * @param int $id
     *
     * @return array
     */
    public function show(int $id): array
    {
        $category = OrganizationCategory::select(['id', 'name'])->where('id', $id)->first();

        if (is_null($category)) { // If the category was not found
            return Api::generateErrorResponse(404, 'NotFoundError', 'The resource you requested for was not found.');
        }

        return [
            'success' => true,
            'payload' => [
                'data' => $category
            ]
        ];
    }

    /**
     * Create a new category.
     *
     * @return array
     */
    public function create(): array
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
     * @return array
     */
    public function update(): array
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
     * @return array
     */
    public function destroy(): array
    {
        $categories = OrganizationCategory::select(['id', 'name'])->get();

        return [
            'success' => true,
            'payload' => $categories
        ];
    }
}
