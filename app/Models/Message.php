<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'message',
    ];

    public function user_messages() {
        return $this->belongsTo(UserMessage::class);
    }
}
