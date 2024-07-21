<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Order;

use Domain\Shop\Order\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => 'nullable|string|min:5',
        ];
    }
}
