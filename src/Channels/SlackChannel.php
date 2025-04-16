<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackChannel implements ChannelInterface
{
    /**
     * Send the OTP via Slack
     *
     * @param string $recipient
     * @param string $otp
     * @param array $data
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool
    {
        try {
            $webhookUrl = config('otp.channels.slack.webhook_url');
            
            if (empty($webhookUrl)) {
                throw new \RuntimeException('Slack webhook URL is not configured');
            }

            $message = $data['message'] ?? "Your OTP code is: {$otp}";
            $message = str_replace('{otp}', $otp, $message);

            $response = Http::post($webhookUrl, [
                'text' => $message,
                'channel' => $recipient,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Slack OTP', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP to Slack: ' . $e->getMessage());
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
        return 'slack';
    }
} 