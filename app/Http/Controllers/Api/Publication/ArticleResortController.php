<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleResort\ArticleResortSupportComment;
use App\Home\Publication\ArticleResort\ArticleResortEvent;
use App\Home\Publication\Article;


class ArticleResortController extends Controller
{
    //展示页
	public function articleResort(Request $request,$id,$name){
		
		$article = Article::find($id)->only('id','title','cid','manage_id');
		if($id && $article){

			$atitle = $article['title'];
			$asking_count = ArticleResort::where([['aid',$id],['pid','0']])->exists() ? '1':'0';
			$helper_count = ArticleResort::where([['aid',$id],['pid','!=','0']])->exists() ? '1':'0';
			//判断评审中是否存在协作计划，如果存在协作计划，接收反对的选项应对协作小组可见，否则，对自管理员可见
	    	$cooperationCount = ArticleCooperation::where([['aid',$id],['status','0']])->exists() ? '1':'0';
            $cooperation = ArticleCooperation::where([['aid',$id],['status','0']])->first();
	    	$manage_id = $article['manage_id'];
	    	$array_encoo_crew_ids = array();
	    	if($cooperationCount)$array_encoo_crew_ids = $cooperation->crews()->pluck('user_id')->toArray();
	    	array_push($array_encoo_crew_ids, $manage_id);
	    	//如果存在求助话题
    		if($asking_count){
    			$data_asking = ArticleResort::where([['aid',$id],['pid','0']])->with('helpers')->orderBy('created_at','DESC')->get();
    		}else{
    			$data_asking = '';
    		}
            // dd($data_asking);

    		if($helper_count){
    			$data_helper = ArticleResort::where([['aid',$id],['pid','!=','0']])->orderBy('created_at','DESC')->get();
    		}else{
    			$data_helper = '';
    		}
    		$helper_comment_count = ArticleResortSupportComment::where([['aid',$id],['type','0']])->exists() ? '1':'0';
    		if($helper_comment_count){
    			$helper_comment = ArticleResortSupportComment::where([['aid',$id],['type','0']])->with('allComment')->orderBy('created_at','DESC')->get();
    		}else{
    			$helper_comment = '';
    		}
    		
    		$help_reject_count = ArticleResortSupportComment::where([['aid',$id],['type','1']])->exists() ? '1':'0';
    		if($help_reject_count){
    			$help_reject = ArticleResortSupportComment::where([['aid',$id],['type','1']])->orderBy('created_at','DESC')->get();
    		}else{
    			$help_reject = '';
    		}
    		$events = ArticleResortEvent::where('aid',$id)->orderBy('created_at','desc')->limit(15)->get();
    		
	    	$data = array(
	    		'basic'		=> $article,
	    		'manage_id'		=> $manage_id,
	    		'asking_count'		=> $asking_count,
	    		'helper_count'		=> $helper_count,
	    		'help_reject_count'		=> $help_reject_count,
	    		'crews'		=> $array_encoo_crew_ids,
	    		'helper_comment_count'		=> $helper_comment_count,
	    		'resorts' => $data_asking,
	    		'helpers' => $data_helper,
	    		'events' => $events,
	    		'helper_comments' => $helper_comment,
	    		'help_rejects' => $help_reject,

	    	);
		}
		return $data;
	}
}
