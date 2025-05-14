<?php

use Illuminate\Support\Facades\Validator;

if (!function_exists('makeDevFormValidator')) {
    function makeDevFormValidator(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);

        $response = function () use ($validator) {
            return response()->json([
                'at' => 'form',
                'errors' => $validator->errors(),
                'data' => $validator->getData(),
            ], 422);
        };

        return [
            'validator' => $validator,
            'response' => $response,
        ];
    }
}

