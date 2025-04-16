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
     * Validate if the OTP is alphanumeric and has the correct length
     *
     * @param string $otp
     * @return bool
     */
    public function validate(string $otp): bool
    {
        return ctype_alnum($otp) && strlen($otp) >= 6;
    }
} 