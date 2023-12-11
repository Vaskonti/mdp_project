<?php

namespace App\Exceptions;

use Exception;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Throwable;

class NoFreeSlotsException extends Exception
{
    public function __construct(string $message = "There are no free slots in the parking lot!", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
