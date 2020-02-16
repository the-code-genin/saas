<?php

namespace App\Helpers;

/**
 * An helper for api methods.
 */
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
}
