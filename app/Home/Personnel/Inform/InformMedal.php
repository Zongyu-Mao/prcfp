<?php

namespace App\Home\Personnel\Inform;

use Illuminate\Database\Eloquent\Model;

class InformMedal extends Model
{
    protected $fillable = ['inform_id','medal_id','createtime'];
    public $timestamps = false;
}
