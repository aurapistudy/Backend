<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var \App\Models\Pengguna */
    public $pengguna;

    /** @var string */
    public $code;

    public function __construct($pengguna, string $code)
    {
        $this->pengguna = $pengguna;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Reset Kata Sandi - ' . config('app.name'))
            ->view('emails.password_reset')
            ->with([
                'pengguna' => $this->pengguna,
                'code' => $this->code,
            ]);
    }
}
