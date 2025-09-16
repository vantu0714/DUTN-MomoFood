<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;

        // ✅ Log luôn khi event được tạo
        Log::info('📨 Event MessageSent được tạo', [
            'id' => $this->message->id,
            'from_id' => $this->message->from_id,
            'to_id' => $this->message->to_id,
            'message' => $this->message->message,
        ]);
    }

    public function broadcastOn(): array
    {
        // ✅ Log khi broadcast
        Log::info('📡 Broadcasting trên channel', [
            'channel' => 'chat.' . $this->message->to_id,
        ]);

        return [
            new PrivateChannel('chat.' . $this->message->to_id),
        ];
    }

    public function broadcastWith(): array
    {
        // ✅ Log dữ liệu gửi đi
        Log::info('📦 Payload gửi đi', [
            'id' => $this->message->id,
            'from_id' => $this->message->from_id,
            'to_id' => $this->message->to_id,
            'message' => $this->message->message,
        ]);

        return [
            'id' => $this->message->id,
            'from_id' => $this->message->from_id,
            'to_id' => $this->message->to_id,
            'message' => $this->message->message,
        ];
    }
}