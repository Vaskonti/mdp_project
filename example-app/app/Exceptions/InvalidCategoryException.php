<?php

namespace App\Exceptions;

use Exception;

class InvalidCategoryException extends Exception
{
    public function __construct(string $message = "The category is invalid!", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
