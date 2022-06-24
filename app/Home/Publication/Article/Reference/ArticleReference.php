<?php

namespace App\Home\Publication\Article\Reference;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Article\ArticleReference\ArticleReferenceAddEvent;
use App\Events\Publication\Article\ArticleReference\ArticleReferenceDeletedEvent;

class ArticleReference extends Model
{
    public $timestamps = true;

    protected $fillable = ['part_id','sort','type','author','title','periodical','publish','pagenumber','creator','revisor'];

    protected function referenceAdd($part_id,$sort,$type,$author,$title,$periodical,$publish,$pagenumber,$creator,$revisor){
    	$result = ArticleReference::create([
            'part_id'   => $part_id,
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
        event(new ArticleReferenceAddEvent($result));
        return $result->id;
    }

    //参考文献的删除,修改仍旧放在控制器中
    protected function articleReferenceDelete($id,$part_id,$sort){
        event(new ArticleReferenceDeletedEvent(ArticleReference::find($id)));
		$result = ArticleReference::where('id',$id)->delete();
    	return $result?'1':'0';
	}
}
