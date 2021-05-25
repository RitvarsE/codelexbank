<?php

namespace App\Mail;

use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class SendVerificationCode extends Mailable
{
    use Queueable, SerializesModels;

    private VerificationCode $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(VerificationCode $code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Verification code')
            ->markdown('Mail.verificationCode')->with('code', $this->code->code);
    }
}
