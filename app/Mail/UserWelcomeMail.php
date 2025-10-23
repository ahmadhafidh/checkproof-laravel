<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $html = "
            <h2>Welcome, {$this->user->name}!</h2>
            <p>Your account ({$this->user->email}) has been successfully created.</p>
            <p>Thank you for joining us!</p>
        ";

        return $this->subject('Welcome to Our System')
                    ->html($html);
    }
}
