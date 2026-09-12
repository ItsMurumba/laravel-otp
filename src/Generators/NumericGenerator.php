<?php

namespace Itsmurumba\Otp\Generators;

use Itsmurumba\Otp\Contracts\GeneratorInterface;

class NumericGenerator implements GeneratorInterface
{
    /**
     * Generate a numeric OTP
     *
     * @param int $length
     * @return string
     */
    public function generate(int $length = 6): string
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        
        return (string) random_int($min, $max);
    }

    /**
     * Validate if the OTP is numeric and has the expected length
     *
     * @param string $otp
     * @param int $length
     * @return bool
     */
    public function validate(string $otp, int $length = 6): bool
    {
        return ctype_digit($otp) && strlen($otp) === $length;
    }
} 