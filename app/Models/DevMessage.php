<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Rolling transcript of the PP dev Telegram group (mirror of Biolinx) — every
 * human and bot message plus downloaded file attachments. Gives the processor
 * conversation memory so references like "this html" resolve to their context.
 */
class DevMessage extends Model
{
    protected $fillable = [
        'chat_id', 'message_id', 'dev_request_id', 'from_name', 'from_username',
        'is_bot', 'reply_to_message_id', 'text', 'file_name', 'file_local_path', 'file_mime',
    ];

    protected $casts = ['is_bot' => 'boolean'];

    public function devRequest()
    {
        return $this->belongsTo(DevRequest::class);
    }
}
