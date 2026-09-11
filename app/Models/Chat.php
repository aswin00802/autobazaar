<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $table = 'chats';

    protected $fillable = [
        'user_id',
        'message',
        'image',
        'video',
        'reply_to_id',
        'message_type',
        'is_deleted',
        'message_time',
        'message_date',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];

    /**
     * The user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The message this one is replying to (if any).
     */
    public function replyTo()
    {
        return $this->belongsTo(Chat::class, 'reply_to_id');
    }

    /**
     * Messages that reply to this one.
     */
    public function replies()
    {
        return $this->hasMany(Chat::class, 'reply_to_id');
    }
}
