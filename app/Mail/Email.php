<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $otp;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $otp)
    {
        //
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Thank you for subscribing and registering")
        ->markdown('emails.emailotp');
    }
}
