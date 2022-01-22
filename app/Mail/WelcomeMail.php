<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $prno;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($prno)
    {
        $this->prno = $prno;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.welcome',[
            'prno' => $this->prno,
        ]);
    }
}
