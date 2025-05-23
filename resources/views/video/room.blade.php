@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-gray-800 rounded-lg overflow-hidden">
            <!-- Meeting Header -->
            <div class="bg-gray-700 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-semibold text-white">{{ $meeting->title }}</h2>
                    <span class="px-3 py-1 bg-blue-600 rounded-full text-sm text-white">Code: {{ $meeting->code }}</span>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="leaveRoom" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        Leave Meeting
                    </button>
                    @if(Auth::user()->id === $meeting->creator_id)
                    <button id="endMeeting" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        End Meeting
                    </button>
                    @endif
                </div>
            </div>

            <!-- Video Container -->
            <div id="zegoContainer" class="w-full h-[600px] bg-gray-900"></div>

            <!-- Meeting Info -->
            <div class="bg-gray-700 px-6 py-4">
                <div class="text-sm text-gray-300">
                    <p><span class="font-medium">Start:</span> {{ $meeting->scheduled_start->format('M d, Y h:i A') }}</p>
                    <p><span class="font-medium">End:</span> {{ $meeting->scheduled_end->format('M d, Y h:i A') }}</p>
                    <p class="mt-2">{{ $meeting->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-900 bg-opacity-90 flex items-center justify-center z-50">
    <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500 mb-4"></div>
        <p class="text-white text-lg">Joining meeting...</p>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const APP_ID = {{ config('services.zego.app_id') }};
    const SERVER_SECRET = '{{ config('services.zego.server_secret') }}';
    const roomID = '{{ $meeting->code }}';
    const userID = '{{ Auth::user()->id }}';
    const userName = '{{ Auth::user()->name }}';

    try {
        const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
            APP_ID, 
            SERVER_SECRET,
            roomID,
            userID,
            userName
        );

        const zp = ZegoUIKitPrebuilt.create(kitToken);
        zp.joinRoom({
            container: document.querySelector("#zegoContainer"),
            scenario: {
                mode: ZegoUIKitPrebuilt.VideoConference,
            },
            showPreJoinView: false,  // Add this line
            turnOnMicrophoneWhenJoining: true,
            turnOnCameraWhenJoining: true,
            onJoinRoom: () => {
                document.getElementById('loadingOverlay').style.display = 'none';
            },
            onJoinRoomFailed: (error) => {
                console.error('Join failed:', error);
                document.getElementById('loadingOverlay').style.display = 'none';
                alert('Failed to join meeting');
            }
        });

        // Add error handler for room connection
        zp.on('roomStateChanged', (state) => {
            if (state === 'DISCONNECTED') {
                document.getElementById('loadingOverlay').style.display = 'none';
                alert('Connection lost');
            }
        });

    } catch (error) {
        console.error('Meeting error:', error);
        document.getElementById('loadingOverlay').style.display = 'none';
        alert('Failed to initialize: ' + error.message);
    }
});
</script>
@endpush
@endsection