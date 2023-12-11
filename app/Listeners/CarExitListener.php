<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CarExitEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

final class CarExitListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(CarExitEvent $event): void
    {
        Log::alert(Carbon::now()->format("d-m-Y H:i:ss")."   ".$event->car->registrationPlate. " exited now");
    }

}
