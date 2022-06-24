<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\Focus\EntryFocusedEvent;
use App\Events\Encyclopedia\Entry\Focus\EntryFocusCanceledEvent;

class EntryFocusUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id','eid'];

    // 用户关注
    protected function entryFocus($user_id,$eid){
    	$result = EntryFocusUser::create([
    		'user_id'	=> $user_id,
    		'eid'	=> $eid
    	]);
    	event(new EntryFocusedEvent($result));
    	return $result->id;
    }

    // 用户取消关注
    protected function entryFocusCancel($user_id,$eid){
    	$res = EntryFocusUser::where([['user_id',$user_id],['eid',$eid]])->first();
    	$result = EntryFocusUser::where('id',$res->id)->delete();
    	event(new EntryFocusCanceledEvent($res));
    	return $result ? 1:0;
    }
}
