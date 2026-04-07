<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'reply_to_message_id',
        'body',
        'source_type',
        'source_id',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function replyParent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }
}
