<?php

declare(strict_types=1);

namespace App\Exceptions;

class DeletingResourceException extends \LogicException
{
    public static function cannotDeleteParentResourceWhileHasChildResource(string $errorMessage): DeletingResourceException
    {
        return new self($errorMessage);
    }
}
