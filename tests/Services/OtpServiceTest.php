<?php

use Itsmurumba\Otp\Services\OtpService;
use Itsmurumba\Otp\Channels\EmailChannel;
use Itsmurumba\Otp\Exceptions\InvalidChannelException;
use Itsmurumba\Otp\Exceptions\RateLimitExceededException;
use Itsmurumba\Otp\Mail\OtpMail;
use Itsmurumba\Otp\Models\Otp;
use Illuminate\Support\Facades\Mail;

test('it generates and sends an OTP via the default sms channel', function () {
    $service = new OtpService();
    $recipient = '+1234567890';

    $otp = $service->generateAndSend($recipient);

    expect($otp)->toBeString()->toHaveLength(6);
    $this->assertDatabaseHas('otps', [
        'identifier' => $recipient,
        'code' => hash('sha256', $otp),
        'channel' => 'sms',
    ]);
});

test('it generates and sends an OTP via a registered channel', function () {
    Mail::fake();

    $service = new OtpService();
    $service->registerChannel(new EmailChannel());
    $recipient = 'test@example.com';

    $otp = $service->generateAndSend($recipient, 'email');

    expect($otp)->toBeString()->toHaveLength(6);
    Mail::assertSent(fn (OtpMail $mail) => $mail->hasTo($recipient));
});

test('it throws for a channel that is not registered', function () {
    $service = new OtpService();

    $service->generateAndSend('test@example.com', 'unregistered-channel');
})->throws(InvalidChannelException::class);

test('it verifies a valid otp and marks it as verified', function () {
    $service = new OtpService();
    $recipient = '+1234567890';

    $otp = $service->generateAndSend($recipient);

    expect($service->verify($recipient, $otp))->toBeTrue();
    $this->assertDatabaseHas('otps', [
        'identifier' => $recipient,
        'code' => hash('sha256', $otp),
        'verified' => true,
    ]);
});

test('it fails verification for an incorrect otp', function () {
    $service = new OtpService();
    $recipient = '+1234567890';

    $service->generateAndSend($recipient);

    expect($service->verify($recipient, 'wrong-otp'))->toBeFalse();
});

test('it enforces rate limiting', function () {
    $service = new OtpService();
    $service->rateLimit(2, 15);
    $recipient = '+1234567890';

    $service->generateAndSend($recipient);
    $service->generateAndSend($recipient);

    $service->generateAndSend($recipient);
})->throws(RateLimitExceededException::class);

test('it rate limits verification attempts independently from generation', function () {
    $service = new OtpService();
    $service->rateLimit(2, 15);
    $recipient = '+1234567890';

    $otp = $service->generateAndSend($recipient);

    expect($service->verify($recipient, 'wrong-1'))->toBeFalse();
    expect($service->verify($recipient, 'wrong-2'))->toBeFalse();

    expect(fn () => $service->verify($recipient, $otp))
        ->toThrow(RateLimitExceededException::class);
});

test('it resolves a container instance wired from config', function () {
    config([
        'otp.length' => 4,
        'otp.expires_in' => 1,
        'otp.rate_limit' => ['max_attempts' => 1, 'decay_minutes' => 15],
    ]);

    $service = $this->app->make('otp');
    $recipient = '+1234567890';

    $otp = $service->generateAndSend($recipient);
    expect($otp)->toHaveLength(4);

    expect(fn () => $service->generateAndSend($recipient))
        ->toThrow(RateLimitExceededException::class);
});

test('it falls back to the configured default channel when none is given', function () {
    Mail::fake();
    config(['otp.default_channels' => 'email']);

    $service = $this->app->make('otp');
    $recipient = 'test@example.com';

    $service->generateAndSend($recipient);

    Mail::assertSent(fn (OtpMail $mail) => $mail->hasTo($recipient));
});

test('it cleans up expired and verified otps', function () {
    $service = new OtpService();

    Otp::create([
        'identifier' => 'expired@example.com',
        'code' => '123456',
        'channel' => 'email',
        'expires_at' => now()->subMinute(),
    ]);

    Otp::create([
        'identifier' => 'active@example.com',
        'code' => '654321',
        'channel' => 'email',
        'expires_at' => now()->addMinutes(5),
    ]);

    $deleted = $service->cleanup();

    expect($deleted)->toBe(1);
    $this->assertDatabaseHas('otps', ['identifier' => 'active@example.com']);
    $this->assertDatabaseMissing('otps', ['identifier' => 'expired@example.com']);
});
