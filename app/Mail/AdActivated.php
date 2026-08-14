<?php

namespace App\Mail;

use App\Models\Ad;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdActivated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ad $ad) {}

    public function build()
    {
        return $this->subject('Your advertisement is now live on DVSE Platform')->view('emails.ad_activated');
    }
}
