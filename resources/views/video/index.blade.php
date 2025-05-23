@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Scheduled Meetings</h1>
        <p class="mt-2 text-gray-600">Schedule or join a video meeting</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Create Meeting -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Schedule New Meeting</h2>
                <form action="{{ route('meeting.store') }}" method="POST">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Title</label>
                        <input type="text" name="title" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Enter meeting title">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="datetime-local" name="scheduled_start" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="datetime-local" name="scheduled_end" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Enter meeting description"></textarea>
                    </div>
                    <button type="submit" 
                        class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Schedule Meeting
                    </button>
                </form>
            </div>

            <!-- Join Meeting Form -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Join Meeting</h2>
                <form action="{{ route('meeting.join') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meeting Code</label>
                        <input type="text" name="code" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                            placeholder="Enter meeting code">
                    </div>
                    <button type="submit" 
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Join Meeting
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Meeting List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Meetings</h3>
                    @if($meetings->count() > 0)
                        @foreach($meetings as $meeting)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-800">{{ $meeting->title }}</h4>
                                    <p class="text-sm text-gray-600">{{ $meeting->description }}</p>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Start:</span> 
                                            {{ \Carbon\Carbon::parse($meeting->scheduled_start)->format('M d, Y h:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">End:</span> 
                                            {{ \Carbon\Carbon::parse($meeting->scheduled_end)->format('M d, Y h:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <span class="font-medium">Code:</span> 
                                            <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $meeting->code }}</span>
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('meeting.show', $meeting->code) }}" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Join
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500 py-4">No upcoming meetings scheduled.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
