<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageList extends Model
{
    use HasFactory;
    protected $table = 'message_list';

    public function getMessage($id)
    {
        return MessageList::where('msg_no', '=', $id)->first();
    }

    public function getMessageOnly($id)
    {
        $result = MessageList::where('msg_no', '=', $id)->first();
        if ($result != null) {
            return $result->msg_text;
        }

        return "";
    }
}
