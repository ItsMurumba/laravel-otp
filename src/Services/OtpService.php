<?php

namespace Itsmurumba\Otp\Services;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Itsmurumba\Otp\Contracts\GeneratorInterface;
use Itsmurumba\Otp\Generators\NumericGenerator;
use Itsmurumba\Otp\Channels\SmsChannel;
use Itsmurumba\Otp\Models\Otp;
use Itsmurumba\Otp\Exceptions\InvalidChannelException;
use Itsmurumba\Otp\Exceptions\RateLimitExceededException;
use Carbon\Carbon;

class OtpService
{
    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var array
     */
    protected $channels = [];

    /**
     * @var int
     */
    protected $length = 6;

    /**
     * @var int
     */
    protected $expiresIn = 5; // minutes

    /**
     * @var RateLimiter
     */
    protected $rateLimiter;

    /**
     * @var RateLimiter
     */
    protected $verifyRateLimiter;

    /**
     * Create a new OTP service instance
     *
     * @param GeneratorInterface|null $generator
     * @param RateLimiter|null $rateLimiter
     */
    public function __construct(?GeneratorInterface $generator = null, ?RateLimiter $rateLimiter = null)
    {
        $this->generator = $generator ?? new NumericGenerator();
        $this->rateLimiter = $rateLimiter ?? new RateLimiter();
        $this->verifyRateLimiter = new RateLimiter('otp:verify-rate-limit:');
        $this->registerDefaultChannels();
    }

    /**
     * Register default channels
     *
     * @return void
     */
    protected function registerDefaultChannels(): void
    {
        $this->registerChannel(new SmsChannel());
    }

    /**
     * Register a new channel
     *
     * @param ChannelInterface $channel
     * @return void
     */
    public function registerChannel(ChannelInterface $channel): void
    {
        $this->channels[$channel->getName()] = $channel;
    }

    /**
     * Set the OTP length
     *
     * @param int $length
     * @return self
     */
    public function length(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Set the OTP expiration time
     *
     * @param int $minutes
     * @return self
     */
    public function expiresIn(int $minutes): self
    {
        $this->expiresIn = $minutes;
        return $this;
    }

    /**
     * Set rate limiting parameters
     *
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return self
     */
    public function rateLimit(int $maxAttempts, int $decayMinutes): self
    {
        $this->rateLimiter->maxAttempts($maxAttempts)
            ->decayMinutes($decayMinutes);
        $this->verifyRateLimiter->maxAttempts($maxAttempts)
            ->decayMinutes($decayMinutes);
        return $this;
    }

    /**
     * Generate and send OTP
     *
     * @param string $recipient
     * @param string|array|null $channels Defaults to config('otp.default_channels') when omitted
     * @param array $data
     * @return string
     * @throws InvalidChannelException
     * @throws RateLimitExceededException
     */
    public function generateAndSend(string $recipient, $channels = null, array $data = []): string
    {
        $channels = $channels ?? config('otp.default_channels', 'sms');

        if ($this->rateLimiter->tooManyAttempts($recipient)) {
            throw new RateLimitExceededException(
                "Too many OTP attempts. Please try again later.",
                $this->rateLimiter->remaining($recipient)
            );
        }

        // Generate OTP
        $otp = $this->generator->generate($this->length);

        // Store in database
        Otp::create([
            'identifier' => $recipient,
            'code' => $this->hash($otp),
            'channel' => is_array($channels) ? implode(',', $channels) : $channels,
            'expires_at' => Carbon::now()->addMinutes($this->expiresIn),
        ]);

        // Send via channels
        $channels = is_array($channels) ? $channels : [$channels];
        
        foreach ($channels as $channel) {
            if (!isset($this->channels[$channel])) {
                throw new InvalidChannelException("Channel {$channel} is not registered.");
            }
            
            $this->channels[$channel]->send($recipient, $otp, $data);
        }

        // Increment rate limit counter
        $this->rateLimiter->hit($recipient);
        
        return $otp;
    }

    /**
     * Verify an OTP
     *
     * @param string $identifier
     * @param string $otp
     * @return bool
     * @throws RateLimitExceededException
     */
    public function verify(string $identifier, string $otp): bool
    {
        if ($this->verifyRateLimiter->tooManyAttempts($identifier)) {
            throw new RateLimitExceededException(
                "Too many OTP verification attempts. Please try again later.",
                $this->verifyRateLimiter->remaining($identifier)
            );
        }

        $otpRecord = Otp::forIdentifier($identifier)
            ->valid()
            ->where('code', $this->hash($otp))
            ->first();

        if (!$otpRecord) {
            $this->verifyRateLimiter->hit($identifier);
            return false;
        }

        // Reset rate limits on successful verification
        $this->rateLimiter->reset($identifier);
        $this->verifyRateLimiter->reset($identifier);

        return $otpRecord->markAsVerified();
    }

    /**
     * Hash an OTP code for storage/comparison
     *
     * @param string $otp
     * @return string
     */
    private function hash(string $otp): string
    {
        return hash('sha256', $otp);
    }

    /**
     * Clean up expired OTPs
     *
     * @return int Number of deleted records
     */
    public function cleanup(): int
    {
        return Otp::where('expires_at', '<', now())
            ->orWhere('verified', true)
            ->delete();
    }
} 