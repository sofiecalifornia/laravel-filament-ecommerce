<?php

declare(strict_types=1);

namespace App\Http\Requests\API\Shop\Order;

use Domain\Shop\Customer\Models\Customer;
use Domain\Shop\OperationHour\Actions\GetOpeningHoursByBranchAction;
use Domain\Shop\Order\Enums\ClaimType;
use Domain\Shop\Order\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'claim_type' => [
                'required',
                Rule::enum(ClaimType::class),
            ],
            'payment_method' => ['required', Rule::enum(PaymentMethod::class)],
            'notes' => 'nullable|string|min:5',
            'claim_at' => [
                'required_if:claim_type,'.ClaimType::delivery->value,
                'prohibited_if:claim_type,'.ClaimType::pickup->value,
                'date_format:Y-m-d H:i',
                'after:now',
                function (string $attribute, string $value, callable $fail) {

                    /** @var \Domain\Shop\Branch\Models\Branch $branch */
                    $branch = $this->route('enabledBranch');

                    $claimType = $this->enum('claim_type', ClaimType::class);

                    if (! $branch->is_operation_hours_enabled || null === $claimType) {
                        return;
                    }

                    match ($claimType) {
                        ClaimType::delivery => $branch->load('operationHoursOnline'),
                        ClaimType::pickup => $branch->load('operationHoursInStore'),
                    };

                    /** @var Customer $customer */
                    $customer = Auth::user();

                    $datetime = now()->parse($value, $customer->timezone);

                    $openingHours = app(GetOpeningHoursByBranchAction::class)
                        ->execute($branch, $claimType->operationHourType());

                    if ($openingHours->isClosedAt($datetime)) {

                        $fail(trans(':Claim_type datetime `:datetime` in not available.', [
                            'claim_type' => $claimType->value,
                            'datetime' => $datetime->toString(),
                        ]));

                    }

                },
            ],
        ];
    }
}
