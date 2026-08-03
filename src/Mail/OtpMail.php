<?php

namespace Itsmurumba\Otp\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $otp
     * @param array $data
     */
    public function __construct(public string $otp, public array $data = [])
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->data['subject'] ?? 'Your OTP Code')
            ->view('otp::email', [
                'otp' => $this->otp,
                'data' => $this->data,
            ]);
    }
}
