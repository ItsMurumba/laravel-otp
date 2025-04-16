<?php

namespace Itsmurumba\Otp\Contracts;

interface GeneratorInterface
{
    /**
     * Generate a new OTP
     *
     * @param int $length
     * @return string
     */
    public function generate(int $length = 6): string;

    /**
     * Validate if the OTP is valid
     *
     * @param string $otp
     * @return bool
     */
    public function validate(string $otp): bool;
} 