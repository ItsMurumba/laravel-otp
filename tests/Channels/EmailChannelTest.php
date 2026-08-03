<?php

use Itsmurumba\Otp\Channels\EmailChannel;
use Itsmurumba\Otp\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

test('it sends OTP via email', function () {
    Mail::fake();
    
    $channel = new EmailChannel();
    $recipient = 'test@example.com';
    $otp = '123456';
    
    $result = $channel->send($recipient, $otp);
    
    expect($result)->toBeTrue();
    
    Mail::assertSent(function (OtpMail $mail) use ($recipient, $otp) {
        return $mail->hasTo($recipient) &&
               str_contains($mail->render(), $otp);
    });
});

test('it handles email sending failures', function () {
    Config::set('mail.driver', 'invalid');
    
    $channel = new EmailChannel();
    $result = $channel->send('test@example.com', '123456');
    
    expect($result)->toBeFalse();
}); 