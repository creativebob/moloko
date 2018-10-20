<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\User;
use App\Lead;

class onAddLeadEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_name;
    public $case_number;

    public function __construct(Lead $lead, User $user)
    {
        $this->user_name = $user->first_name . ' ' . $user->second_name;
        $this->case_number = $lead->case_number;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
