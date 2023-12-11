<?php

declare(strict_types = 1);

namespace App\Listeners;

use App\Events\CarRegisterEvent;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

final class CarRegisterListener
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
    public function handle(CarRegisterEvent $event): void
    {
        Log::alert(Carbon::now()->format("d-m-Y H:i:ss")."   ".$event->car->registrationPlate. " was registered");
    }

}
