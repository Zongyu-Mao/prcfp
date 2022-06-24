<?php

namespace App\Home\Personnel\MessageInform;

use Illuminate\Database\Eloquent\Model;

class MessageInformMedal extends Model
{
    protected $fillable = ['inform_id','medal_id','createtime'];
    public $timestamps = false;
}
