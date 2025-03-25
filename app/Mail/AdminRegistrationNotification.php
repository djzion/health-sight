<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegistrationPending extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $userData;

    /**
     * Create a new message instance.
     */
    public function __construct(protected User $user)
    {
        $this->userData = [
            'userName' => $user->full_name ?? 'User',
            'userEmail' => $user->email ?? 'user@example.com'
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New User Registration Requires Approval',
            to: $this->userData['userEmail']
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.registration.admin-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
