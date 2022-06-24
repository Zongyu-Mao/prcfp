<?php

namespace App\Models\Globalization\GlobalUserAdvise;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalUserAdviseComment extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'user_id','advise_id','content','createtime'
    ];

    // 作者
    public function creator(){
        return $this->hasOne('App\Models\User','id','user_id');
    }

    protected function newComment($user_id,$advise_id,$content,$createtime) {
        $result = GlobalUserAdviseComment::create([
            'user_id'   => $user_id,
            'advise_id'   => $advise_id,
            'content'   => $content,
            'createtime'   => $createtime
        ]);
        return $result;
    }

}
