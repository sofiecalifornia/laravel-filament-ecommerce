<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    /** @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string> */
    public function rules(): array
    {
        return [
            'email' => ['required', Rule::email()],
            'password' => 'required|string',
        ];
    }
}
