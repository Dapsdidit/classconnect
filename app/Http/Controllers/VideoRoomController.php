<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VideoRoomController extends Controller
{
    // Display the meetings dashboard
    public function index()
    {
        $meetings = Meeting::orderBy('scheduled_start', 'asc')->get();

        $events = $meetings->map(function ($meeting) {
            $start = $meeting->scheduled_start instanceof Carbon
                ? $meeting->scheduled_start->format('Y-m-d\TH:i:s')
                : Carbon::parse($meeting->scheduled_start)->format('Y-m-d\TH:i:s');

            $end = $meeting->scheduled_end instanceof Carbon
                ? $meeting->scheduled_end->format('Y-m-d\TH:i:s')
                : Carbon::parse($meeting->scheduled_end)->format('Y-m-d\TH:i:s');

            return [
                'title' => $meeting->title,
                'start' => $start,
                'end' => $end,
                'url' => route('meeting.show', $meeting->code),
            ];
        })->toArray();

        return view('video.index', compact('meetings', 'events'));
    }

    // Store a new meeting
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'description' => 'nullable|string'
        ]);

        $meeting = Meeting::create([
            'title' => $request->title,
            'code' => Str::random(10),
            'creator_id' => auth()->id(), // Add creator_id from authenticated user
            'scheduled_start' => $request->scheduled_start,
            'scheduled_end' => $request->scheduled_end,
            'description' => $request->description,
        ]);

        return redirect()->route('meeting.show', $meeting->code);
    }

    // Join meeting by code (handles join form submission)
    public function join(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $meeting = Meeting::where('code', $request->code)->first();

        if (!$meeting) {
            return back()->withErrors(['code' => 'Invalid meeting code.']);
        }

        return redirect()->route('meeting.show', $meeting->code);
    }

    // Show a specific meeting
    public function show($code)
    {
        $meeting = Meeting::where('code', $code)->firstOrFail();
        return view('video.simple-room', compact('meeting'));
    }

    public function endMeeting($code)
    {
        $meeting = Meeting::where('code', $code)->firstOrFail();
        
        // Ensure only the meeting creator can end it
        if (auth()->id() !== $meeting->creator_id) {
            abort(403);
        }

        // Update meeting status
        $meeting->update(['is_active' => false]);

        return redirect()->route('video.index')->with('status', 'Meeting ended successfully');
    }
}
