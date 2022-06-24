<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Publication\ArticleDiscussion\ArticleDiscussionEvent;
use App\Home\Publication\Article;

class ArticleDiscussionController extends Controller
{
    //首页展示
    public function articleDiscussion(Request $request,$id,$enctitle){
    	
    	$article = Article::find($id);
    	//如果请求的id存在
    	if($id && $article){
	    	//查看该词条下是否有讨论内容
	    	$discuss_count = ArticleDiscussion::where('aid',$id)->exists() ? '1':'0';
	    	$oppose_count = ArticleOpponent::where('aid',$id)->exists() ? '1':'0';
	    	$advise_count = ArticleAdvise::where('aid',$id)->exists() ? '1':'0';
	    	//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见
	    	$encoo_count = ArticleCooperation::where([['aid',$id],['status','0']])->exists() ? '1':'0';
            $cooperation = ArticleCooperation::where([['aid',$id],['status','0']])->first();
	    	if($encoo_count){
	    		//如果存在活跃的协作计划，取得协作计划成员组
	    		$initiate_id = $cooperation->manage_id;
                $array_encoo_crew_ids = $cooperation->crews()->pluck('user_id')->toArray();
	    		array_push($array_encoo_crew_ids, $initiate_id);
	    	}else{
	    		//如果没有活跃的协作计划，评审由自管理员托管
	    		$initiate_id = Article::where('id',$id)->first()->manager_id;
	    		$array_encoo_crew_ids = array();
	    	}
				$manager_id = Article::where('id',$id)->first()->manager_id;
			//取得反对内容
			if($oppose_count){
				$data_oppose = ArticleOpponent::where([['aid',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
			}else{
				$data_oppose = '';
			}
			//取得建议内容
			if($advise_count){
				$data_advise = ArticleAdvise::where([['aid',$id],['pid',0]])->with('allAdvise')->get();
			}else{
				$data_advise = '';
			}
			//取得普通讨论内容
			if($discuss_count){
				$data_discuss = ArticleDiscussion::where([['aid',$id],['pid',0]])->with('allDiscuss')->get();
			}else{
				$data_discuss = '';
			}
			//取得词条讨论的事件内容
			$discuss_event_count = ArticleDiscussionEvent::where('aid',$id)->exists() ? '1':'0';;
			if($discuss_event_count){
				$data_events = ArticleDiscussionEvent::where('aid',$id)->orderBy('created_at','DESC')->get();
			}else{
				$data_events = '';
			}
	    	$data = array(
	    		'basic'		=> $article,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'opposes'	=> $data_oppose,
	    		'advises'	=> $data_advise,
	    		'discussions'	=> $data_discuss,
	    		'events'		=> $data_events
	    	);
    	}
    	return $data;
    }
}
