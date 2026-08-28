<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Delivers a lander's companion guide PDF to a captured lead (e.g. the PT-141
 * lander popup promises "the guide, sent to your inbox"). The PDF is attached
 * directly from storage/app/guides so delivery never depends on a reachable
 * download link.
 */
class LanderGuideMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $guideKey,
        public string $guideName
    ) {}

    public function build(): static
    {
        $mail = $this->subject('Your ' . $this->guideName . ' Protocol Guide')
            ->view('emails.lander-guide', ['guideName' => $this->guideName]);

        $path = storage_path('app/guides/' . $this->guideKey . '.pdf');
        if (is_file($path)) {
            $mail->attach($path, [
                'as' => $this->guideName . ' Protocol Guide.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
