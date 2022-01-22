<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $detailMail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($detailMail)
    {
        $this->detailMail = $detailMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $template = "";
        
        if ($this->detailMail['template'] == "MAIL_PR02") {
            $template = "emails.MAIL_PR02";
        } else if ($this->detailMail['template'] == "MAIL_PR03") {
            $template = "emails.MAIL_PR03";
        }

        return $this->markdown($template,[
            'detailMail' => $this->detailMail,
        ]);
    }
}
