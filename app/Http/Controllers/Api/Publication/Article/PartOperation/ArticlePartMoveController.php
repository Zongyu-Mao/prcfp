<?php

namespace App\Http\Controllers\Api\Publication\Article\PartOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Publication\Article\ArticlePart;

class ArticlePartMoveController extends Controller
{
    //章节前移
    public function articlePartMove(Request $request){
    	// 该id是content的id
    	$data = $request->data;
    	$id = $data['id'];
    	$sort = $data['sort'];
    	$aid = $data['aid'];
    	$isForward = $data['isForward'];
    	$result = false;
    	// return $request;
    	if($sort > 1 && $isForward){
	    	if(ArticlePart::where([['aid',$aid],['sort',$sort-1]])->exists()){
	    		ArticlePart::where([['aid',$aid],['sort',$sort-1]])->update(['sort'=>$sort]);
	    	}
    		$result = ArticlePart::where('id',$id)->update(['sort' => $sort-1]);
    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    	}elseif(!$isForward){
    		$max = max(ArticlePart::where('aid',$aid)->pluck('sort')->toArray());
    		if($sort != $max){
		    	if(ArticlePart::where([['aid',$aid],['sort',$sort+1]])->exists()){
		    		ArticlePart::where([['aid',$aid],['sort',$sort+1]])->decrement('sort');
		    	}
	    		$result = ArticlePart::where('id',$id)->increment('sort');
	    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    		}
    	}
        if($result)$parts=ArticlePart::where('aid',$aid)->orderBy('sort','asc')->get();
        $part = $parts->where('id',$id)->first();
        $contents = $part->contents;
        $references = $part->references;
        return ['success'=>$result ? true:false,'parts'=>$parts,'part'=>$part,'contents'=>$contents,'references'=>$references];
    }
}
