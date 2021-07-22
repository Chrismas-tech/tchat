<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'sender_id',
        'receiver_id',
    ];

    public function message() {
        return $this->hasOne(Message::class);
    }

    public function users() {
        return $this->belongsTo(User::class);
    }
}
