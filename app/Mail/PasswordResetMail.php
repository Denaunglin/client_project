<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link, $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($link, $data)
    {
        $this->link = $link;
        $this->data = $data;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('passwordResetMail.resetmail');
    }
}
