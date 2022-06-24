<?php

namespace App\Home\Personnel\JudgementInform;

use Illuminate\Database\Eloquent\Model;

class JudgementInformMedal extends Model
{
    protected $fillable = ['inform_id','medal_id','createtime'];
    public $timestamps = false;
}
