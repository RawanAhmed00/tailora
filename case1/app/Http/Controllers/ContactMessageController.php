<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    function index()
    {
        $messages = ContactMessage::with('user')->get();

        return response()->json($messages);
    }

    function show(int $id)
    {
        $message = ContactMessage::with('user')->find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        return response()->json($message);
    }

    
   public function store(Request $request)
{
    $request->validate([
        'message' => 'required|string',
    ]);

    $message = ContactMessage::create([
        'user_id' => auth()->id(),
        'message' => $request->message,
    ]);

    return response()->json([
        'message' => 'Created Successfully',
        'data' => $message
    ], 201);
}

    
   public function update(Request $request, int $id)
{
    $message = ContactMessage::find($id);

    if (!$message) {
        return response()->json([
            'message' => 'Message not found'
        ], 404);
    }

    $request->validate([
        'status' => 'required|in:pending,under_review,read,solved',
    ]);

    $message->update([
        'status' => $request->status
    ]);

    return response()->json([
        'message' => 'Status Updated Successfully',
        'data' => $message
    ]);
}
    
     function destroy(int $id)
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        $message->delete();

        return response()->json([
            'message' => 'Deleted Successfully'
        ]);
    }
}