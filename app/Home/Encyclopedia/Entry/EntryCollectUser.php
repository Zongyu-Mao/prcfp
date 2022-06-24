<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\Focus\EntryCollectedEvent;
use App\Events\Encyclopedia\Entry\Focus\EntryCollectCanceledEvent;

class EntryCollectUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','eid'];

    protected function entryCollect($user_id,$eid){
    	$result = EntryCollectUser::create([
    		'user_id'	=> $user_id,
    		'eid'		=> $eid
    	]);
    	event(new EntryCollectedEvent($result));
    	return $result->id;
    }

    // 用户取消收藏
    protected function entryCollectCancel($user_id,$eid){
    	$res = EntryCollectUser::where([['user_id',$user_id],['eid',$eid]])->first();
    	$result = EntryCollectUser::where('id',$res->id)->delete();
    	event(new EntryCollectCanceledEvent($res));
    	return $result ? 1:0;
    }
}
