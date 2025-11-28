<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountVerification extends Mailable
{
    use Queueable, SerializesModels;

    public $verifyCode, $name, $tokenId;
    public function __construct($name, $verifyCode, $tokenId)
    {
        $this->name = $name;
        $this->verifyCode = $verifyCode;
        $this->tokenId = $tokenId;
    }

    public function build()
    {
        return $this->subject('GO_TRENER - potwierdzenie adresu email')
                    ->view('verification');
    }

}
