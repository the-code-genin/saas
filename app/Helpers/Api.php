<?php

namespace App\Helpers;

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
}
