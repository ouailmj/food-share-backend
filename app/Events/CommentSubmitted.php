<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $from,$to,$id_annonce,$objet;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($from,$to,$id_annonce,$objet)
    {
        $this->from = $from;
        $this->to = $to;
        $this->id_annonce = $id_annonce;
        $this->objet = $objet;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('my-channel');
    }

    public function broadcastAs(){
        return 'object-submitted';
    }
}
