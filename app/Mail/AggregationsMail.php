<?php

declare(strict_types = 1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class AggregationsMail extends Mailable
{

    use Queueable;
    use SerializesModels;

    const TEMPLATE = "email_aggregations";

    private int $sumGenerated;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(int $sumPaid, private int $carsRegistered, private $date)
    {
        $this->sumGenerated = $sumPaid;
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
