<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ParticipantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data; // Explicitly define type

    /**
     * Create a new message instance.
     *
     * @param array $data Participant details
     */
    public function __construct(array $data)
    {
        $this->data = $data; // Ensure data is always an array
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Welcome to the Programme')
            ->view('emails.participant_welcome')
            ->with([
                'data' => $this->data,
                'password' => $this->data['password'] // Pass the password
            ]);
    }

}
