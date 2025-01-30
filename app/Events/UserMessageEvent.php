<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\MongoChat;
use App\Models\MongoUser;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $messageId;
    private $chatId;
    private $messageUserId;
    private $userImage;
    private $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(MongoChat $message, MongoUser $user)
    {
        $this->message = $message->message;
        $this->messageId = $message->id;
        $this->chatId = $message->conversation_id;
        $this->messageUserId = $user->id;
        $this->userImage = asset($user->image());
        $this->user = $user;
    }

    public function broadcastAs()
    {
        return 'getUserChatMessage';
    }

    public function broadcastWith()
    {
        return [
            'chatId' => $this->chatId,
            'message' => $this->message,
            'message_id' => $this->messageId,
            'messageUserId' => $this->messageUserId,
            'userImage' => $this->userImage
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user-message.' . $this->chatId);
    }
}
