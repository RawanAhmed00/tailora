<?php

namespace App\Http\Controllers;
use App\Events\MessageSent;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    //1-GET/ api/tour-guide/chats
    public function index()
    {
        //latest chat with everyone
        $authId=auth()->id();
        $conversations=Message::with(['sender','receiver'])
        ->where('sender_id',$authId)
        ->orWhere('receiver_id',$authId)
        ->latest()->get()
        ->unique(function ($item) use ($authId){
            return $item->sender_id == $authId ? $item->receiver_id : $item->sender_id;
        })->values();
        return MessageResource::collection($conversations);
    }

    //2-GET/ api/tour-guide/chats/{id}
    public function getMessages($id)
    {
        //ascending order of messages with a specefic person
        $authId=auth()->id();
        $messages=Message::with('sender')
        ->where(function ($q) use ($authId, $id){
            $q->where('sender_id', $authId)->where('receiver_id',$id);
        })
        ->orWhere(function ($q) use ($authId, $id){
            $q->where('sender_id',$id)->where('receiver_id',$authId);
        })
        ->orderBy('created_at','asc')->get();
        return MessageResource::collection($messages);
    }

    //3-POST/ api/tour_guide/chats/{id}/messages
    public function sendMessage(StoreMessageRequest $request, $id)
    {
        //Send a message to a person with specefic id
        $message=Message::create([
            'sender_id'=>auth()->id(),
            'receiver_id'=>$id,
            'message'=>$request->message,
        ]);
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'status'=>'Message sent successfully',
            'data'=> new MessageResource($message->load('sender'))
        ],201);
    }

    public function markAsRead($id)
    {
        //when a person opens the chat of the sender, messages marked as seen(read)
        $authId=auth()->id();

        Message::where('sender_id',$id)
        ->where('receiver_id',$authId)
        ->where('is_read',false)
        ->update(['is_read'=>true]);

        return response()->json(['status'=>'Message marked as read']);
    }

    public function unreadCount()
    {
        $count=Message::where('receiver_id',auth()->id())
        ->where('is_read',false)
        ->count();

        return response()->json(['unread_count'=>$count]);
    }

    public function deleteMessage($messageId)
    {
        $message = Message::where('id',$messageId)
        ->where('sender_id',auth()->id())
        ->firstOrFail();
        $message->delete();
        return response()->json(['status'=>'Message deleted suuccessfully']);
    }
}
