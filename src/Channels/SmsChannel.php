<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;

class SmsChannel implements ChannelInterface
{
    /**
     * Send the OTP via SMS
     *
     * @param string $recipient
     * @param string $otp
     * @param array $data
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool
    {
        // TODO: Implement actual SMS sending logic
        // This is a placeholder implementation
        return true;
    }

    /**
     * Get the channel name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'sms';
    }
} 