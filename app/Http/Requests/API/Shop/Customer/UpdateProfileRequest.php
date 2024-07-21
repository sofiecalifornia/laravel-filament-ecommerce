<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Customer;

use Domain\Shop\Customer\DataTransferObjects\CustomerData;
use Domain\Shop\Customer\Enums\Gender;
use Domain\Shop\Customer\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \Domain\Shop\Customer\Models\Customer $customer */
        $customer = Auth::user();

        return [
            'email' => [
                'required',
                Rule::email(),
                Rule::unique(Customer::class)->ignoreModel($customer),
            ],
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
