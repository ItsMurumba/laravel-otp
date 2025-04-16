<?php

namespace Itsmurumba\Otp\Channels;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel implements ChannelInterface
{
    /**
     * Send the OTP via WhatsApp
     *
     * @param string $recipient
     * @param string $otp
     * @param array $data
     * @return bool
     */
    public function send(string $recipient, string $otp, array $data = []): bool
    {
        try {
            $apiKey = config('otp.channels.whatsapp.api_key');
            $apiUrl = config('otp.channels.whatsapp.api_url');
            
            if (empty($apiKey) || empty($apiUrl)) {
                throw new \RuntimeException('WhatsApp API credentials are not configured');
            }

            $message = $data['message'] ?? "Your OTP code is: {$otp}";
            $message = str_replace('{otp}', $otp, $message);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'to' => $recipient,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Failed to send WhatsApp OTP', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP to WhatsApp: ' . $e->getMessage());
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
        return 'whatsapp';
    }
} 