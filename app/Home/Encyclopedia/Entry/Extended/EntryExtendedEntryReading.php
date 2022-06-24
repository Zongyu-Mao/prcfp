<?php

namespace App\Home\Encyclopedia\Entry\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingAddEvent;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedReadingDeletedEvent;

class EntryExtendedEntryReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['eid','extended_id','creator_id'];

    protected function entryExtendedAdd($eid,$extended_id,$creator_id){
    	$result = EntryExtendedEntryReading::create([
            'eid'		=> $eid,
	     	'extended_id'	=> $extended_id,
	     	'creator_id'	=> $creator_id,
        ]);
        event(new EntryExtendedReadingAddEvent($result));
        return $result->id;
    }

    // 删除引用
    protected function entryExtendedDelete($eid,$extended_id){
        $res = EntryExtendedEntryReading::where([['eid',$eid],['extended_id',$extended_id]])->first();
        $result = EntryExtendedEntryReading::where('id',$res->id)->delete();
        event(new EntryExtendedReadingDeletedEvent($res));
        return $result?1:0;
    }
}
