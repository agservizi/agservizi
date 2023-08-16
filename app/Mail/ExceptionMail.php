<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ExceptionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The body of the message.
     *
     * @var \Throwable
     */
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $file = $this->content->getFile();
        $stringaADestra = Str::after($file, 'htdocs');
        $phpStormLink = 'phpstorm://open?file=/Users/andrea/Documents/laravel/' . config('configurazione.cartella_locale','cartella_locale_non_definita') . '/' . $stringaADestra . '&line=' . $this->content->getLine();

        return $this->view('Mail.exceptionMail')
            ->with([
                'exception' => $this->content,
                'phpStormLink' => $phpStormLink
            ]);
    }
}
