<?php

namespace Itsmurumba\Otp\Generators;

use Itsmurumba\Otp\Contracts\GeneratorInterface;

class AlphanumericGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    protected $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Generate an alphanumeric OTP
     *
     * @param int $length
     * @return string
     */
    public function generate(int $length = 6): string
    {
        $charactersLength = strlen($this->characters);
        $otp = '';

        for ($i = 0; $i < $length; $i++) {
            $otp .= $this->characters[random_int(0, $charactersLength - 1)];
        }

        return $otp;
    }

    /**
     * Validate if the OTP is alphanumeric and has the expected length
     *
     * @param string $otp
     * @param int $length
     * @return bool
     */
    public function validate(string $otp, int $length = 6): bool
    {
        return ctype_alnum($otp) && strlen($otp) === $length;
    }
} 