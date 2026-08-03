<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Itsmurumba\Otp\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailChannel implements ChannelInterface
{
    /**
     * Send the OTP via email
     *
     * @param string $recipient
     * @param string $otp
     * @param array $data
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool
    {
        try {
            Mail::to($recipient)->send(new OtpMail($otp, $data));

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the channel name
     *
     * @return string
     */
    public function getName(): string
    {
        return 'email';
    }
} 