<?php


namespace MgSoftware\Mutex;

use Exception;
use Throwable;

class FailedException extends Exception
{
    public function __construct(string $message = 'Failed to acquire mutex.', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
