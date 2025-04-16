<?php

namespace Itsmurumba\Otp\Exceptions;

use Exception;

class RateLimitExceededException extends Exception
{
    /**
     * @var int
     */
    protected $remaining;

    /**
     * Create a new exception instance
     *
     * @param string $message
     * @param int $remaining
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "Too many OTP attempts", $remaining = 0, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->remaining = $remaining;
    }

    /**
     * Get the number of attempts remaining
     *
     * @return int
     */
    public function getRemaining(): int
    {
        return $this->remaining;
    }
} 