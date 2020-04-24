<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class Api
{
    /**
     * Generate an api error response.
     *
     * @param integer $code
     * @param string $type
     * @param string $message
     * @return array
     */
    public static function generateErrorResponse(int $code, string $type, string $message): array
    {
        return [
            'success' => false,
            'error' => [
                'code' => $code,
                'type' => $type,
                'message' => $message,
            ]
        ];
    }

    /**
     * Return the first validation error from a validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return string|null
     */
    public static function getFirstValidationError(Validator $validator): ?string
    {
        if (!$validator->fails()) return null;

        $errors = $validator->errors();
        $fields = $errors->keys();

        return $errors->first($fields[0]);
    }

    /**
     * Generate API payload.
     *
     * @param \Illuminate\Http\Request $request
     * @param Builder $results
     *
     * @return array
     */
    public static function getPayload(Request $request, Builder $results): array
    {
        $payload = [];

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

        return $payload;
    }
}
