<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

final class UnknownCardTypeException extends Exception
{
    public function __construct(
        string $message = "This is not a valid card type!",
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

}
