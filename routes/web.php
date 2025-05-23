<?php

use App\Http\Controllers\VideoRoomController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard redirect
    Route::get('/dashboard', function () {
        return redirect()->route('video.index');
    })->name('dashboard');

    // Video Meeting Routes
    Route::get('/video', [VideoRoomController::class, 'index'])->name('video.index');
    Route::post('/meetings', [VideoRoomController::class, 'store'])->name('meeting.store'); // FIXED: changed 'create' to 'store'
    Route::get('/meeting/{code}', [VideoRoomController::class, 'show'])->name('meeting.show');
    Route::post('/meeting/join', [VideoRoomController::class, 'join'])->name('meeting.join');
    
    // Add this new route for ending meetings
    Route::post('/meeting/{code}/end', [VideoRoomController::class, 'endMeeting'])->name('meeting.end');
    
    // Legacy Room Routes (can be removed if not needed)
    Route::get('/rooms', [RoomController::class, 'index'])->name('room.index');
    Route::post('/room/create', [RoomController::class, 'create'])->name('room.create');
    Route::post('/room/join-by-code', [RoomController::class, 'join'])->name('room.join.code');
    Route::delete('/api/room/{room}/delete', [RoomController::class, 'destroy']);
    Route::get('/room/{roomId}', [RoomController::class, 'show'])->name('room.show');
    Route::post('/api/room/{roomId}/join', [RoomController::class, 'join'])->name('room.join');
    Route::post('/api/room/{roomId}/leave', [RoomController::class, 'leave'])->name('room.leave');
});

// Homepage route
Route::get('/', function () {
    return view('welcome');
})->name('home');
