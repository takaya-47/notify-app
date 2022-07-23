<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeatherMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Collection
     */
    protected $weather;

    /**
     * Create a new message instance.
     *
     * @param  Collection $weather
     * @return void
     */
    public function __construct(Collection $weather)
    {
        $this->weather = $weather;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'))
                    ->subject('本日の天気予報です！')
                    ->view('emails.weather')
                    ->with(['weather_data_array' => $this->weather]); // メールビューでは{$weather_data}で使用できる
    }
}
