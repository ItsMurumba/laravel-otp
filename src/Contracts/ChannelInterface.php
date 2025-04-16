<?php

namespace Itsmurumba\Otp\Contracts;

interface ChannelInterface
{
    /**
     * Send the OTP to the recipient
     *
     * @param string $recipient
     * @param string $otp
     * @param array $data Additional data for the channel
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool;

    /**
     * Get the name of the channel
     *
     * @return string
     */
    public function getName(): string;
} 