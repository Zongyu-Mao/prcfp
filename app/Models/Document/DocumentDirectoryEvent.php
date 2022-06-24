<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDirectoryEvent extends Model
{
    use HasFactory;
    protected $fillable = ['did','user_id','username','content','createtime'];
    public $timestamps = false;

    // 事件的添加
    protected function directoryEventAdd($did,$user_id,$username,$content,$createtime){
    	$eventArray = array(
    		'did'		=> $did,
    		'user_id'	=> $user_id,
    		'username'	=> $username,
            'content'   => $content,
    		'createtime'	=> $createtime,
    		);
    	$directoryEvent = new DocumentDirectoryEvent;
    	$result = $directoryEvent -> fill($eventArray) -> save();
    	return $result;
    }
}
