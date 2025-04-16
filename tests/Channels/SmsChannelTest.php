<?php

use Itsmurumba\Otp\Channels\SmsChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

test('it sends OTP via SMS', function () {
    Http::fake([
        'api.sms.com/send' => Http::response(['status' => 'success'], 200)
    ]);
    
    Config::set('otp.channels.sms.api_key', 'test-api-key');
    Config::set('otp.channels.sms.api_url', 'https://api.sms.com/send');
    
    $channel = new SmsChannel();
    $recipient = '+1234567890';
    $otp = '123456';
    
    $result = $channel->send($recipient, $otp);
    
    expect($result)->toBeTrue();
    
    Http::assertSent(function ($request) use ($recipient, $otp) {
        return $request->url() === 'https://api.sms.com/send' &&
               $request['to'] === $recipient &&
               str_contains($request['message'], $otp) &&
               $request['api_key'] === 'test-api-key';
    });
});

test('it handles SMS sending failures', function () {
    Http::fake([
        'api.sms.com/send' => Http::response(['status' => 'error'], 500)
    ]);
    
    Config::set('otp.channels.sms.api_key', 'test-api-key');
    Config::set('otp.channels.sms.api_url', 'https://api.sms.com/send');
    
    $channel = new SmsChannel();
    $result = $channel->send('+1234567890', '123456');
    
    expect($result)->toBeFalse();
}); 