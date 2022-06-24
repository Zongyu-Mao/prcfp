<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceAddEvent;
use App\Events\Encyclopedia\Entry\EntryReference\EntryReferenceDeletedEvent;

class EntryReference extends Model
{
    //关联表格
    public $timestamps = true;

    protected $fillable = ['entry_id','sort','type','author','title','periodical','publish','pagenumber','creator','revisor'];

    protected function referenceAdd($entry_id,$sort,$type,$author,$title,$periodical,$publish,$pagenumber,$creator,$revisor){
    	$result = EntryReference::create([
            'entry_id'   => $entry_id,
            'sort'   => $sort,
            'type'   => $type,
            'author' => $author,
            'title'=> $title,
            'periodical'=> $periodical,
            'publish'=> $publish,
            'pagenumber'=> $pagenumber,
            'creator'=>$creator,
            'revisor'=>$revisor
        ]);
        event(new EntryReferenceAddEvent($result));
        return $result->id;
    }

    //参考文献的删除,修改仍旧放在控制器中
    protected function referenceDelete($id,$entry_id,$sort){
        event(new EntryReferenceDeletedEvent(EntryReference::find($id)));
		$result = EntryReference::where('id',$id)->delete();
    	return $result?'1':'0';
	}

}
