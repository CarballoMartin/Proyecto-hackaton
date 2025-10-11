<?php

namespace App\Events;

use App\Models\Institucion;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InstitucionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $institucion;
    public $user;
    public $passwordTemporal;

    /**
     * Create a new event instance.
     */
    public function __construct(Institucion $institucion, User $user, string $passwordTemporal)
    {   
        
        $this->institucion = $institucion;
        $this->user = $user;
        $this->passwordTemporal = $passwordTemporal;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
