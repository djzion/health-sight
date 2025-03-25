<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class _UserRegistrationPending extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;

    public function __construct(User $user)
    {
        $this->userData = [
            'userName' => $user->full_name ?? 'User',
            'userEmail' => $user->email ?? 'user@example.com'
        ];
    }

    public function build()
    {
        return $this->subject('Your HealthSight Registration is Pending Approval')
                    ->view('emails.plain.user-pending');
    }
}
