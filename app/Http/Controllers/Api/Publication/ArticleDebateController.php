<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleDebate\ArticleDebateEvent;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleDebate\ArticleDebateComment;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\Article;

class ArticleDebateController extends Controller
{
    //首页显示
    public function articleDebate(Request $request,$id,$title){
    	$article = Article::find($id);
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_debateAll = '';
    	$data_comments = '';
    	$starRecord = [];
    	$debateFrom = '';
    	if($id && $title==$article->title){
    		// 取得debate数据,否则为空
    		if(ArticleDebate::where('aid',$id)->exists()){
    			$data_debateAll = ArticleDebate::where('aid',$id)->orderBy('created_at','DESC')->get();
    		}
    	}
    	return $data = array(
    		'debate_all'	=> $data_debateAll,
    		);
    }

    //单debate的详情
    public function debate(Request $request){
    	$id = $request->id;
    	$type = $request->type;
    	$typeID = $request->type_id;
    	$data_comments = '';
    	$starRecords = [];
    	$debateFrom = '';
    	// return $request;
    	// return ArticleDebate::where('eid',$id)->get();
    	// return ArticleDebate::where([['eid',$id],['type',$type],['type_id',$typeID]])->first();;
    	if($type && $typeID){
			// 这里得到具体的debate了
			$debate = ArticleDebate::where([['aid',$id],['type',$type],['type_id',$typeID]])->with('getStars')->first();
            if($debate){
                $debate_id = $debate->id;
                // $starRecord = $debate->getStars();
                // $starRecord = $debate->getStars->pluck('user_id')->toArray();
                // array_push($starRecord,$debate->Aauthor_id);
                // array_push($starRecord,$debate->Bauthor_id);
                // if($debate->referee_id){
                // 	array_push($starRecord,$debate->referee_id);
                // };
                // $starRecords = array_unique($starRecord);
                //判断网友留言是否存在
                if(ArticleDebateComment::where([['aid',$id],['debate_id',$debate_id],['pid',0]])->exists()){
					$data_comments = ArticleDebateComment::where([['aid',$id],['debate_id',$debate_id],['pid',0]])->with('allComment')->orderBy('created_at','DESC')->get();
				}
    			
    		}
			// dd($debate);
			if($type == 1){
				$debateFrom = ArticleReviewOpponent::find($typeID)->title;
			}elseif($type == 2){
				$debateFrom = ArticleOpponent::find($typeID)->title;
			}
	    	
    	}
    	return $data = array(
    		'debate'		=> $debate,
    		'comments'		=> $data_comments,
    		'debateFrom'	=> $debateFrom
    		);
    	
    }
}
