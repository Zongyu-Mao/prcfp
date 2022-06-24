<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Document\DocumentCreatedEvent;
use App\Events\Document\DocumentModifiedEvent;
use Laravel\Scout\Searchable;

class Document extends Model
{
    use HasFactory, Searchable;
    public $timestamps = true;

    protected $fillable = [
        'cid', 'title', 'content','status','creator_id','lasteditor_id'
    ];

    //关联分类表
    public function directory(){
    	return $this -> hasOne('App\Models\Document\DocumentDirectory','id','cid');
    }

    //创建文档
    protected function documentCreate($cid,$title,$content,$creator_id,$lasteditor_id) {
        $result = Document::create([
            'cid'   => $cid,
            'title' => $title,
            'content' => $content,
            'creator_id'=>$creator_id,
            'lasteditor_id' => $lasteditor_id
        ]);
        event(new DocumentCreatedEvent($result));
        return $result->id;
    }

    //编辑，此处对应正文内容第一次经过创建者编辑
    protected function documentModify($did,$content,$lasteditor_id){
    	$result = Document::where('id',$did)->update([
            'content'   => $content,
            'lasteditor_id' => $lasteditor_id
        ]);
        event(new DocumentModifiedEvent(Document::find($did)));
        return $result;
    }
}
