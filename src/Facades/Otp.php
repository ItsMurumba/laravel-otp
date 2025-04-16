<?php

namespace Itsmurumba\Otp\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Itsmurumba\Otp\Services\OtpService length(int $length)
 * @method static \Itsmurumba\Otp\Services\OtpService expiresIn(int $minutes)
 * @method static string generateAndSend(string $recipient, $channels = 'sms', array $data = [])
 * @method static bool verify(string $otp)
 * 
 * @see \Itsmurumba\Otp\Services\OtpService
 */
class Otp extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'otp';
    }
}
