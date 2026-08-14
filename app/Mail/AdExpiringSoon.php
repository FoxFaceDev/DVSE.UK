<?php

namespace App\Mail;

use App\Models\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdExpiringSoon extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ad $ad) {}

    public function build()
    {
        return $this->subject('Your DVSE Platform advertisement expires in one week')->view('emails.ad_expiring');
    }
}
