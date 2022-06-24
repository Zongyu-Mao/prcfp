<?php

namespace App\Models\Globalization;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','content','editor_id'
    ];
    // 关联user
    public function creator(){
        return $this->hasOne('App\Models\User','id','user_id');
    }
    public function editor(){
        return $this->hasOne('App\Models\User','id','editor_id');
    }
    //创建，下面两个方法目前均不涉及事件
    protected function newNotification($user_id,$content) {
        $result = GlobalNotification::create([
            'user_id'   => $user_id,
            'content'   => $content
        ]);
        return $result->id;
    }
    // 更换
    protected function modify($id,$editor_id,$content) {
        $result = GlobalNotification::where('id',$id)->update([
            'editor_id'   => $editor_id,
            'content'   => $content
        ]);
        return $result;
    }
}
