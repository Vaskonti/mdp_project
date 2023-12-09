<?php

namespace App\Listeners;

use App\Events\CarRegisterEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CarRegisterListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CarRegisterEvent  $event
     * @return void
     */
    public function handle(CarRegisterEvent $event)
    {
        Log::alert(Carbon::now()->format("d-m-Y H:i:ss")."   ".$event->car->registrationPlate. " was registered");
    }
}
