<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserVerificationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User $user */
    private $user;

    /** @var UserVerificationToken $token */
    private $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, UserVerificationToken $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.welcome', [ 'user' => $this->user, 'token' => $this->token ])
            ->subject('Welcome To SaaS!');
    }
}
