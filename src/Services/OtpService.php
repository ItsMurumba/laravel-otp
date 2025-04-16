<?php

namespace Itsmurumba\Otp\Services;

use Itsmurumba\Otp\Contracts\ChannelInterface;
use Itsmurumba\Otp\Contracts\GeneratorInterface;
use Itsmurumba\Otp\Generators\NumericGenerator;
use Itsmurumba\Otp\Channels\SmsChannel;
use Itsmurumba\Otp\Exceptions\InvalidChannelException;

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
     * Create a new OTP service instance
     *
     * @param GeneratorInterface|null $generator
     */
    public function __construct(?GeneratorInterface $generator = null)
    {
        $this->generator = $generator ?? new NumericGenerator();
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
     * Generate and send OTP
     *
     * @param string $recipient
     * @param string|array $channels
     * @param array $data
     * @return string
     * @throws InvalidChannelException
     */
    public function generateAndSend(string $recipient, $channels = 'sms', array $data = []): string
    {
        $otp = $this->generator->generate($this->length);
        
        $channels = is_array($channels) ? $channels : [$channels];
        
        foreach ($channels as $channel) {
            if (!isset($this->channels[$channel])) {
                throw new InvalidChannelException("Channel {$channel} is not registered.");
            }
            
            $this->channels[$channel]->send($recipient, $otp, $data);
        }
        
        return $otp;
    }

    /**
     * Verify an OTP
     *
     * @param string $otp
     * @return bool
     */
    public function verify(string $otp): bool
    {
        return $this->generator->validate($otp);
    }
} 