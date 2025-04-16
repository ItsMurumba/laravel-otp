<?php

use Itsmurumba\Otp\Channels\TelegramChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

test('it sends OTP via Telegram', function () {
    Http::fake([
        'api.telegram.org/bot*/sendMessage' => Http::response(['ok' => true], 200)
    ]);
    
    Config::set('otp.channels.telegram.bot_token', 'test-bot-token');
    
    $channel = new TelegramChannel();
    $recipient = '123456789';
    $otp = '123456';
    
    $result = $channel->send($recipient, $otp);
    
    expect($result)->toBeTrue();
    
    Http::assertSent(function ($request) use ($recipient, $otp) {
        return str_contains($request->url(), 'api.telegram.org/bot') &&
               $request['chat_id'] === $recipient &&
               str_contains($request['text'], $otp);
    });
});

test('it handles Telegram sending failures', function () {
    Http::fake([
        'api.telegram.org/bot*/sendMessage' => Http::response(['ok' => false], 500)
    ]);
    
    Config::set('otp.channels.telegram.bot_token', 'test-bot-token');
    
    $channel = new TelegramChannel();
    $result = $channel->send('123456789', '123456');
    
    expect($result)->toBeFalse();
}); 