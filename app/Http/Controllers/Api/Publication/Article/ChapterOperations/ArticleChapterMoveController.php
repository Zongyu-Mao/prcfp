<?php

namespace App\Http\Controllers\Api\Publication\Article\ChapterOperations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article;

class ArticleChapterMoveController extends Controller
{
    //章节前移
    public function articleChapterMove(Request $request){
    	// 该id是content的id
    	$id = $request->content_id;
    	$sort = $request->sort;
    	$part_id = $request->part_id;
    	$isForward = $request->isForward;
    	$result = false;
    	// return $request;
    	if($sort > 1 && $isForward){
    		$sortToChange = ArticleContent::where([['part_id',$part_id],['sort',$sort-1]])->get();
	    	if(count($sortToChange)){
	    		ArticleContent::where([['part_id',$part_id],['sort',$sort-1]])->update(['sort'=>$sort]);
	    	}
    		$result = ArticleContent::where('id',$id)->update(['sort' => $sort-1]);
    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    	}elseif(!$isForward){
    		$max = max(ArticleContent::where('part_id',$part_id)->pluck('sort')->toArray());
    		if($sort != $max){
    			$sortToChange = ArticleContent::where([['part_id',$part_id],['sort',$sort+1]])->get();
		    	if(count($sortToChange)){
		    		ArticleContent::where([['part_id',$part_id],['sort',$sort+1]])->update(['sort'=>$sort]);
		    	}
	    		$result = ArticleContent::where('id',$id)->update(['sort' => $sort+1]);
	    		// 注意，sortToChange在最后的的话，会产生两个sort=sort-1的东东所以决定先改需要改的的，再改本id的sort	
    		}
    	}
        if($result)$contents=ArticleContent::where('part_id',$part_id)->orderBy('sort','asc')->get();
        return ['success'=>$result ? true:false,'contents'=>$contents];
    }
}
