<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Customer;

use Domain\Shop\Customer\DataTransferObjects\CustomerData;
use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', Rule::email(), Rule::unique(Customer::class)],
            'password' => [...Password::required(), 'confirmed'],
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'gender' => ['required', Rule::enum(Gender::class)],
        ];
    }

    public function toDTO(): CustomerData
    {
        $data = $this->validated();
        $data['gender'] = Gender::from($data['gender']);

        return new CustomerData(...$data);
    }
}
