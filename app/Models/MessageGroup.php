<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'status',
    ];

    public function message_group_members() {
        return $this->hasMany(MessageGroupMember::class);
    }

    public function user_messages() {
        return $this->hasMany(UserMessage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
