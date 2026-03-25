<?php

namespace App\Modules\CRM\Exceptions;

use RuntimeException;

final class DealLifecycleViolationException extends RuntimeException
{
    public static function forClosedUpdate(): self
    {
        return new self('Closed deals cannot be updated.');
    }

    public static function forClosedTransition(): self
    {
        return new self('Closed deals cannot be moved to another terminal state.');
    }
}
