<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Illuminate\Support\Facades\Mail;

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
            Mail::send('otp::email', [
                'otp' => $otp,
                'data' => $data,
            ], function ($message) use ($recipient, $data) {
                $message->to($recipient)
                    ->subject($data['subject'] ?? 'Your OTP Code');
            });

            return true;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Failed to send OTP email: ' . $e->getMessage());
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