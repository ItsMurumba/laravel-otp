<?php

use Itsmurumba\Otp\Services\OtpService;
use Itsmurumba\Otp\Channels\EmailChannel;
use Itsmurumba\Otp\Channels\SmsChannel;
use Itsmurumba\Otp\Channels\WhatsAppChannel;
use Itsmurumba\Otp\Channels\TelegramChannel;
use Itsmurumba\Otp\Channels\SlackChannel;
use Illuminate\Support\Facades\Config;

test('it generates and sends OTP via email', function () {
    Config::set('otp.default_channel', 'email');
    Config::set('otp.channels.email.enabled', true);
    
    $service = new OtpService();
    $recipient = 'test@example.com';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeArray()
        ->toHaveKey('otp')
        ->toHaveKey('expires_at');
});

test('it generates and sends OTP via SMS', function () {
    Config::set('otp.default_channel', 'sms');
    Config::set('otp.channels.sms.enabled', true);
    
    $service = new OtpService();
    $recipient = '+1234567890';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeArray()
        ->toHaveKey('otp')
        ->toHaveKey('expires_at');
});

test('it generates and sends OTP via WhatsApp', function () {
    Config::set('otp.default_channel', 'whatsapp');
    Config::set('otp.channels.whatsapp.enabled', true);
    
    $service = new OtpService();
    $recipient = '+1234567890';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeArray()
        ->toHaveKey('otp')
        ->toHaveKey('expires_at');
});

test('it generates and sends OTP via Telegram', function () {
    Config::set('otp.default_channel', 'telegram');
    Config::set('otp.channels.telegram.enabled', true);
    
    $service = new OtpService();
    $recipient = '123456789';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeArray()
        ->toHaveKey('otp')
        ->toHaveKey('expires_at');
});

test('it generates and sends OTP via Slack', function () {
    Config::set('otp.default_channel', 'slack');
    Config::set('otp.channels.slack.enabled', true);
    
    $service = new OtpService();
    $recipient = '#general';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeArray()
        ->toHaveKey('otp')
        ->toHaveKey('expires_at');
});

test('it handles invalid channel', function () {
    Config::set('otp.default_channel', 'invalid');
    
    $service = new OtpService();
    $recipient = 'test@example.com';
    
    $result = $service->generateAndSend($recipient);
    
    expect($result)->toBeFalse();
}); 