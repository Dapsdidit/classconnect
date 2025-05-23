<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Add this import
use App\Models\Room;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::where('active', true)->get();
        return view('video.index', compact('rooms'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'passkey' => 'nullable|string|max:255'
        ]);

        $room = Room::create([
            'name' => $request->name,
            'code' => Str::random(6),
            'creator_id' => Auth::id(),
            'passkey' => $request->passkey,
            'active' => true
        ]);

        // Create a room participant record for the creator
        $room->participants()->create([
            'user_id' => Auth::id()
        ]);

        return redirect()->route('room.show', $room);
    }

    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:rooms,code',
            'passkey' => 'nullable|string'
        ]);

        $room = Room::where('code', $request->code)->firstOrFail();

        // Check if passkey is required and matches
        if ($room->passkey && $room->passkey !== $request->passkey) {
            return back()->withErrors(['passkey' => 'Invalid room passkey']);
        }

        // Check if user is already a participant
        if (!$room->participants()->where('user_id', Auth::id())->exists()) {
            $room->participants()->create([
                'user_id' => Auth::id()
            ]);
        }

        return redirect()->route('room.show', $room);
    }

    public function show(Room $room)
    {
        // Check if user is a participant or creator
        if (Auth::id() !== $room->creator_id && !$room->participants()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('room.index')
                ->withErrors(['access' => 'You are not authorized to access this room']);
        }

        return view('video.room', compact('room'));
    }

    public function destroy(Room $room)
    {
        if (Auth::id() !== $room->creator_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}