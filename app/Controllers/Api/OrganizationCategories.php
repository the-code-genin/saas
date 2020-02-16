<?php
namespace App\Controllers\Api;

use App\Models\OrganizationCategory;
use Cradle\Controller;
use Psr\Http\Message\ServerRequestInterface;

class OrganizationCategories extends Controller
{
    /**
     * Get all organization categories.
     * 
     * @return array
     */
    protected function index(ServerRequestInterface $request, object $params)
    {
        $categories = OrganizationCategory::select(['id', 'name'])->get();

        return [
            'success' => true,
            'payload' => $categories
        ];
    }
}
