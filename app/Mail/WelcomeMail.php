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
        if ($this->detailMail['template'] == "MAIL_PR01") {
            $template = "emails.MAIL_PR01";
        } else if ($this->detailMail['template'] == "MAIL_PR02") {
            $template = "emails.MAIL_PR02";
        } else if ($this->detailMail['template'] == "MAIL_PR01_Approval") {
            $template = "emails.MAIL_PR01_Approval";
        } else if ($this->detailMail['template'] == "MAIL_PR02_Approval") {
            $template = "emails.MAIL_PR02_Approval";
        } else if ($this->detailMail['template'] == "MAIL_PR03_Approval") {
            $template = "emails.MAIL_PR03_Approval";
        }

        return $this->markdown($template,['detailMail' => $this->detailMail,])
            ->subject($this->detailMail['subject']);
    }
}
