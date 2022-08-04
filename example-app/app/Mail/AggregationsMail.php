<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AggregationsMail extends Mailable
{
    use Queueable, SerializesModels;
    const TEMPLATE = "email_aggregations";

    private int $sumGenerated;
    private int $carsRegistered;
    private $date;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $sumPaid, int $carsRegistered, $date)
    {
        $this->sumGenerated = $sumPaid;
        $this->carsRegistered = $carsRegistered;
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view(self::TEMPLATE)
                    ->subject('Aggregations for:' )
                    ->to(['admin@lab08.example.com'])
                    ->with($this->sumGenerated)
                    ->with($this->carsRegistered);
    }
}
