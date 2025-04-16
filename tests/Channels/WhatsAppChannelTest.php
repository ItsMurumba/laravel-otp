<?php

use Itsmurumba\Otp\Channels\WhatsAppChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

test('it sends OTP via WhatsApp', function () {
    Http::fake([
        'api.whatsapp.com/send' => Http::response(['status' => 'success'], 200)
    ]);
    
    Config::set('otp.channels.whatsapp.api_key', 'test-api-key');
    Config::set('otp.channels.whatsapp.api_url', 'https://api.whatsapp.com/send');
    
    $channel = new WhatsAppChannel();
    $recipient = '+1234567890';
    $otp = '123456';
    
    $result = $channel->send($recipient, $otp);
    
    expect($result)->toBeTrue();
    
    Http::assertSent(function ($request) use ($recipient, $otp) {
        return $request->url() === 'https://api.whatsapp.com/send' &&
               $request['to'] === $recipient &&
               str_contains($request['message'], $otp) &&
               $request['api_key'] === 'test-api-key';
    });
});

test('it handles WhatsApp sending failures', function () {
    Http::fake([
        'api.whatsapp.com/send' => Http::response(['status' => 'error'], 500)
    ]);
    
    Config::set('otp.channels.whatsapp.api_key', 'test-api-key');
    Config::set('otp.channels.whatsapp.api_url', 'https://api.whatsapp.com/send');
    
    $channel = new WhatsAppChannel();
    $result = $channel->send('+1234567890', '123456');
    
    expect($result)->toBeFalse();
}); 