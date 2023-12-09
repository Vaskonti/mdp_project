<?php

namespace App\Listeners;

use App\Events\CarExitEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CarExitListener
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
     * @param  \App\Events\CarExitEvent  $event
     * @return void
     */
    public function handle(CarExitEvent $event)
    {
        Log::alert(Carbon::now()->format("d-m-Y H:i:ss")."   ".$event->car->registrationPlate. " exited now");
    }
}
