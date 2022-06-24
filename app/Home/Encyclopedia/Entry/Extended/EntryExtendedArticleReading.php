<?php

namespace App\Home\Encyclopedia\Entry\Extended;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingAddEvent;
use App\Events\Encyclopedia\Entry\EntryExtendedReading\EntryExtendedArticleReadingDeletedEvent;

class EntryExtendedArticleReading extends Model
{
    public $timestamps = false;

    protected $fillable = ['eid','extended_id','creator_id'];

    protected function entryExtendedArticleAdd($eid,$extended_id,$creator_id){
    	$result = EntryExtendedArticleReading::create([
            'eid'		=> $eid,
	     	'extended_id'	=> $extended_id,
	     	'creator_id'	=> $creator_id,
        ]);
        event(new EntryExtendedArticleReadingAddEvent($result));
        return $result->id;
    }

    // 删除引用
    protected function entryExtendedArticleDelete($eid,$extended_id){
        $res = EntryExtendedArticleReading::where([['eid',$eid],['extended_id',$extended_id]])->first();
        $result = EntryExtendedArticleReading::where('id',$res->id)->delete();
        event(new EntryExtendedArticleReadingDeletedEvent($res));
        return $result?1:0;
    }
}
