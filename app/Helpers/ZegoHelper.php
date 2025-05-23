<?php

use Zego\ZegoServerSDK;

if (!function_exists('generateZegoToken')) {
    function generateZegoToken($appID, $serverSecret, $roomID, $userID, $userName)
    {
        $zegoSDK = new ZegoServerSDK($appID, $serverSecret);
        
        $payload = [
            'room_id' => $roomID,
            'user_id' => $userID,
            'user_name' => $userName,
            'expire_ts' => time() + 3600 // 1 hour expiration
        ];

        return $zegoSDK->generateToken($payload);
    }
}