<?php

namespace App\Home\Encyclopedia\Entry\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingAddEvent;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedExamReadingDeletedEvent;

class EntryExtendedExamReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['eid','extended_id','creator_id'];

    protected function entryExtendedExamAdd($eid,$extended_id,$creator_id){
    	$result = EntryExtendedExamReading::create([
            'eid'		=> $eid,
	     	'extended_id'	=> $extended_id,
	     	'creator_id'	=> $creator_id,
        ]);
        event(new EntryExtendedExamReadingAddEvent($result));
        return $result->id;
    }

    // 删除引用
    protected function entryExtendedExamDelete($eid,$extended_id){
        $res = EntryExtendedExamReading::where([['eid',$eid],['extended_id',$extended_id]])->first();
        $result = EntryExtendedExamReading::where('id',$res->id)->delete();
        event(new EntryExtendedExamReadingDeletedEvent($res));
        return $result?1:0;
    }
}
