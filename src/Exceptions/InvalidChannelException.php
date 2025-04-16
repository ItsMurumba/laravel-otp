<?php

namespace Itsmurumba\Otp\Exceptions;

use Exception;

class InvalidChannelException extends Exception
{
    /**
     * Create a new exception instance
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "Invalid channel provided", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 