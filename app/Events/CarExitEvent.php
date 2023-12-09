<?php

namespace App\Events;

use App\Models\Mongo\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CarExitEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $car;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Vehicle $car)
    {
        $car->exited = Carbon::now()->toDateTimeLocalString();
        $car->save();
        $this->car = $car;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
