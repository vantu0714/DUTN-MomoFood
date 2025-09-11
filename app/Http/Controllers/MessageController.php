<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Lấy tin nhắn giữa user và admin
    public function index($userId)
    {
        $messages = Message::where(function ($q) use ($userId) {
            $q->where('from_id', auth()->id())->where('to_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('from_id', $userId)->where('to_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    // Gửi tin nhắn
    public function store(Request $request)
    {
        $message = Message::create([
            'from_id' => auth()->id(),
            'to_id' => $request->to_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json(['message' => $message]);
    }

    // Danh sách user đã nhắn tin
    public function adminIndex()
    {
        $users = User::whereHas('messages', function ($q) {
            $q->where('to_id', auth()->id())
                ->orWhere('from_id', auth()->id());
        })
            ->with([
                'messages' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }
            ])
            ->get()
            ->sortByDesc(function ($user) {
                return optional($user->messages->first())->created_at;
            });

        return view('admin.messages.index', compact('users'));
    }


    // Lấy tin nhắn giữa admin và 1 user
    public function getMessagesWithUser($userId)
    {
        $messages = Message::where(function ($q) use ($userId) {
            $q->where('from_id', $userId)
                ->where('to_id', auth()->id());
        })
            ->orWhere(function ($q) use ($userId) {
                $q->where('from_id', auth()->id())
                    ->where('to_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $user = User::find($userId);

        return response()->json(['user' => $user, 'messages' => $messages]);
    }

    // Admin gửi tin nhắn
    public function adminSend(Request $request)
    {
        $request->validate([
            'to_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'from_id' => auth()->id(),
            'to_id' => $request->to_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

}
