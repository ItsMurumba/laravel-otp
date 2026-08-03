<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel implements ChannelInterface
{
    /**
     * Send the OTP via Telegram
     *
     * @param string $recipient Telegram chat ID
     * @param string $otp
     * @param array $data
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool
    {
        try {
            $botToken = config('otp.channels.telegram.bot_token');

            if (empty($botToken)) {
                throw new \RuntimeException('Telegram bot token is not configured');
            }

            $message = $data['message'] ?? "Your OTP code is: {$otp}";
            $message = str_replace('{otp}', $otp, $message);

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $recipient,
                'text' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send Telegram OTP', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP to Telegram: ' . $e->getMessage());
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
        return 'telegram';
    }
}
