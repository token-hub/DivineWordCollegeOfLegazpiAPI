<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $mail = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
<<<<<<< HEAD
        return $this->to('dwclinfo@dwc-legazpi.edu')
=======
        return $this->to('johnsuyang2118@gmail.com')
>>>>>>> ac4110461f3f37877bea1fdee1527936bfaef239
            ->subject($this->mail['subject'])
            ->view('mail')
            ->with(['mail' => $this->mail]);
    }
}
