<?php

namespace BlackIT\PrivyID\Events;

use BlackIT\PrivyID\PrivyIDDocument;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PrivyIDDocumentStatusChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var $document PrivyIDDocument
     */
    public $document;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($document)
    {
        $this->document = $document;

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
