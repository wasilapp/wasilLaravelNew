<?php
namespace App\Exceptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class CustomValidationException extends ValidationException
{
    protected function formatErrors(Validator $validator)
    {
        return [
            'message' => 'هناك أخطاء في البيانات المدخلة.',
            'errors' => $validator->errors(),
        ];
    }
}
