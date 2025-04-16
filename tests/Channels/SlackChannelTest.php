<?php

use Itsmurumba\Otp\Channels\SlackChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

test('it sends OTP via Slack', function () {
    Http::fake([
        'slack.com/api/chat.postMessage' => Http::response(['ok' => true], 200)
    ]);
    
    Config::set('otp.channels.slack.webhook_url', 'https://slack.com/api/chat.postMessage');
    
    $channel = new SlackChannel();
    $recipient = '#general';
    $otp = '123456';
    
    $result = $channel->send($recipient, $otp);
    
    expect($result)->toBeTrue();
    
    Http::assertSent(function ($request) use ($recipient, $otp) {
        return $request->url() === 'https://slack.com/api/chat.postMessage' &&
               $request['channel'] === $recipient &&
               str_contains($request['text'], $otp);
    });
});

test('it handles Slack sending failures', function () {
    Http::fake([
        'slack.com/api/chat.postMessage' => Http::response(['ok' => false], 500)
    ]);
    
    Config::set('otp.channels.slack.webhook_url', 'https://slack.com/api/chat.postMessage');
    
    $channel = new SlackChannel();
    $result = $channel->send('#general', '123456');
    
    expect($result)->toBeFalse();
}); 