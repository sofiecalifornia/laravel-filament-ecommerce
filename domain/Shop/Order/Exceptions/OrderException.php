<?php

declare(strict_types=1);

namespace Domain\Shop\Order\Exceptions;

use Exception;

class OrderException extends Exception
{
    public static function orderItemRequired(): self
    {
        return new self('Order item required.');
    }
}
