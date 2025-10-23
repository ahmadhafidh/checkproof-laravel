<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminNewUserMail extends Mailable
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
            <h3>New User Registered</h3>
            <ul>
                <li>Name: {$this->user->name}</li>
                <li>Email: {$this->user->email}</li>
                <li>Created at: {$this->user->created_at}</li>
            </ul>
        ";

        return $this->subject('New User Registered')
                    ->html($html);
    }
}
