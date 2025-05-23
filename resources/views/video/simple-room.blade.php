<!DOCTYPE html>
<html>
<head>
    <title>{{ $meeting->title }} - ClassConnect</title>
    <style>
        #root {
            width: 100vw;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div id="root"></div>
    
    <script src="https://unpkg.com/@zegocloud/zego-uikit-prebuilt/zego-uikit-prebuilt.js"></script>
    <script>
    window.onload = function() {
        const APP_ID = 689293340;
        const SERVER_SECRET = "bba00ea3fb053e23e50c1376a4a15879";
        const roomID = "{!! addslashes($meeting->code) !!}"; 
        const userID = "{!! Auth::user()->id !!}";
        const userName = "{!! addslashes(Auth::user()->name) !!}";

        const kitToken = ZegoUIKitPrebuilt.generateKitTokenForTest(
            APP_ID, 
            SERVER_SECRET,
            roomID,
            userID.toString(),
            userName
        );

        const zp = ZegoUIKitPrebuilt.create(kitToken);
        zp.joinRoom({
            container: document.querySelector("#root"),
            scenario: {
                mode: ZegoUIKitPrebuilt.VideoConference,
            },
            turnOnMicrophoneWhenJoining: true,
            turnOnCameraWhenJoining: true
        });
    }
    </script>
</body>
</html>