<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function fetchMessagesFromUserToTeacher(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $senderId = session('LoggedUserInfo');

        $messages = Chat::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json(['messages' => $messages]);
    }

    public function sendMessageFromUserToTeacher(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:teachers,id',
        ]);

        // Create a new chat message
        $chat = new Chat();
        $chat->sender_id = session('LoggedUserInfo');
        $chat->receiver_id = $request->input('receiver_id');
        $chat->message = $request->input('message');
        $chat->seen = 0; // Default to not seen
        $chat->save();

        //event(new SendUserMessage($chat));

        return response()->json(['success' => true, 'message' => 'Message sent successfully']);
    }

    public function sendMessageFromTeacherToUser(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Create a new chat message
        $chat = new Chat();
        $chat->sender_id = session('LoggedTeacherInfo');
        $chat->receiver_id = $request->input('receiver_id');
        $chat->message = $request->input('message');
        $chat->seen = 0; // Default to not seen
        $chat->save();

        //event(new SendUserMessage($chat));

        return response()->json(['success' => true, 'message' => 'Message sent successfully']);
    }


    public function fetchTeacherChatList(Request $request)
    {
        $LoggedTeacherInfo = Teacher::find(session('LoggedTeacherInfo'));

        $teacherId = $LoggedTeacherInfo->id;

        $chats = Chat::where(function ($query) use ($teacherId) {
            $query->where('sender_id', $teacherId)
                ->orWhere('receiver_id', $teacherId);
        })->orderBy('created_at', 'asc')->get();

        $userIds = [];

        if ($chats->count() > 0) {
            foreach ($chats as $chat) {
                if ($chat->sender_id != $teacherId) {
                    $userIds[] = $chat->sender_id;
                }
                if ($chat->receiver_id != $teacherId) {
                    $userIds[] = $chat->receiver_id;
                }
            }

            $userIds = array_unique($userIds);
            $users = User::whereIn('id', $userIds)->get();
        } else {
            $users = [];
        }

        return response()->json([
            'users' => $users
        ]);
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'receiver_id' => 'required|integer|exists:users,id', // Ensure the receiver_id is a valid user id
        ]);

        $LoggedTeacherInfo = Teacher::find(session('LoggedTeacherInfo'));
        if (!$LoggedTeacherInfo) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to send a message',
            ]);
        }

        $message = new Chat();
        $message->sender_id = $LoggedTeacherInfo->id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->save();
        //broadcast(new SendAdminMessage($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }
    public function fetchOfUserMessages(Request $request)
    {
        $receiverId = $request->input('receiver_id');

        // Fetch the logged-in admin using the session
        $teacherId = session('LoggedTeacherInfo');
        $LoggedTeacherInfo = Teacher::find($teacherId);

        if (!$LoggedTeacherInfo) {
            return response()->json([
                'error' => 'Teacher not found. You must be logged in to access messages.'
            ], 404);
        }

        // Fetch messages between the admin and the specified seller
        $messages = Chat::where(function ($query) use ($teacherId, $receiverId) {
            $query->where('sender_id', $teacherId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($teacherId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $teacherId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function fetchOfTeacherMessages(Request $request)
    {
        // Validate the input to ensure teacher_id is provided

        $receiverId = $request->input('receiver_id');
        $loggedInUserId = session('LoggedUserInfo'); // Directly get user ID from session

        // Fetch messages where the user is either the sender or receiver
        $messages = Chat::where(function ($query) use ($receiverId, $loggedInUserId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $loggedInUserId);
        })->orWhere(function ($query) use ($receiverId, $loggedInUserId) {
            $query->where('sender_id', $loggedInUserId)
                ->where('receiver_id', $receiverId);
        })->orderBy('created_at', 'asc')->get();

        return response()->json([
            'messages' => $messages
        ]);
    }
}
