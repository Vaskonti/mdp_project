<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Mongo\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

final class CarExitEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

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
     */
    public function broadcastOn(): Channel|array
    {
        return new PrivateChannel('channel-name');
    }

}
