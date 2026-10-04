<?php

namespace App\Http\Controllers;

use App\Events\UserMessageEvent;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\MongoChat;
use App\Models\MongoConversation;
use App\Models\MongoUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserMessageController extends Controller
{

    public function send(Request $request, $chat_id)
    {
        $user = auth('user')->user();
        if (trim($request->message) == '' || !isset($user)) {
            return response()->json(['success' => false]);
        }
        try {
            $chat = new MongoChat();
            $chat->sender_id = $user->id;
            $chat->message = $request->message;
            $chat->conversation_id = $chat_id;
            $chat->save();

            $conversation = MongoConversation::find($chat_id);

            $lastMessages = $conversation->last_messages ?? [];
            $lastMessages[] = [
                'sender_id' => $chat->sender_id,
                'message' => $chat->message,
                'timestamp' => $chat->created_at,
            ];
            if (count($lastMessages) > 15) {
                $lastMessages = array_slice($lastMessages, -15);
            }
            $conversation->last_messages = $lastMessages;
            $conversation->save();

            // broadcast(new UserMessageEvent($chat, $user))->toOthers();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function messages()
    {
        $user = auth('user')->user();
        $chats = MongoConversation::where('user_1', $user->id)->orWhere('user_2', $user->id)->where('last_messages', '!=', null)->get();
        return view('user.received-message', compact('chats'));
    }

    public function start($user_id)
    {
        $user = auth('user')->user();
        $chat_id = $user->chatIdWithUser($user_id);
        if ($chat_id != null) {
            return redirect()->route('user.show.message', $chat_id);
        } else {
            $newChat = new MongoConversation();
            $newChat->user_1 = $user->id;
            $newChat->user_2 = $user_id;
            $newChat->save();
            return redirect()->route('user.show.message', $newChat->id);
        }
    }

    public function show($id)
    {
        $user = auth('user')->user();
        $chat = MongoConversation::find($id);
        if ($chat->user_1  != $user->id && $chat->user_2  != $user->id) {
            return redirect()->route('home');
        }

        if (!isset($chat)) {
            abort(404);
        }

        if ($chat->user_1 == $user->id) {
            $user2 = MongoUser::find($chat->user_2);
        } else {
            $user2 = MongoUser::find($chat->user_1);
        }

        return view('user.message', compact('chat', 'user', 'user2'));
    }
}
