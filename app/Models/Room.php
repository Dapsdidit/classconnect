<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'code',
        'active',
        'creator_id',
        'passkey'
    ];

    public function participants()
    {
        return $this->hasMany(RoomParticipant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}