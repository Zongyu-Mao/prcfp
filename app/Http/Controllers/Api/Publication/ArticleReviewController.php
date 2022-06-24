<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;
use App\Home\Publication\ArticleReview\ArticleReviewEvent;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\ArticleReview\ArticleReviewRecord;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleDebate;
use Input;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ArticleReviewController extends Controller
{
    //评审计划首页
    public function articleReview(Request $request,$id,$enctitle){
        // 评审只有在协作计划活跃且词条反对均处理掉的情况下才能开启,为了避免冲突，协作计划的结束时间应该大于评审结束时间
        $article = Article::find($id);
        $cooperation = ArticleCooperation::where([['aid',$id],['status','0']])->first();
        $review = ArticleReview::where([['aid',$id],['status','0']])->with('getReviewRecord')->first();
        $active_oppose_count = ArticleOpponent::where([['aid',$id],['status','0']])->count();
        $active_debate_count = ArticleDebate::where([['aid',$id],['status','0']])->count();
        if($review){
            $rid = $review->id;
            $data_kpi = ArticleReview::where([['aid',$id],['status','0']])->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = ArticleReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = ArticleReviewAdvise::where([['rid',$rid],['pid',0]])->with('allAdvise')->get();;

            $reviewDiscussions = ArticleReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();;

            $reviewEvents = ArticleReviewEvent::where('rid',$rid)->orderBy('created_at','desc')->limit(10)->get();;
            // dd($rid);
            // dd($reviewEvents);

            $reviewRecord = ArticleReviewRecord::where('review_id',$rid)->get();
            $agreeNum = ArticleReviewRecord::getAgreeNum($rid);
            $opposeNum = ArticleReviewRecord::getOpposeNum($rid);
            $neutralNum = ArticleReviewRecord::getNeutralNum($rid);
            $reviewArr = $reviewRecord->pluck('user_id')->toArray();
            $myReview = $reviewRecord->filter(function($item){
                return $item->user_id==auth('api')->user()->id; 
            });
            $myReview = $myReview->first();
        }else{
            $data_kpi = null;
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = null;
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = null;

            $reviewDiscussions = null;

            $reviewEvents = null;

            $reviewRecord = null;
            $agreeNum = null;
            $opposeNum = null;
            $neutralNum = null;
            $reviewArr = null;
            $myReview = null;
        }


    	$articleTitle = $article->title;
    	$articleLevel = $article->level;
        $manage_id = $article->manage_id;
        $cooperationCrews = [];
    	if($cooperation)$cooperationCrews = $cooperation->crews()->pluck('user_id')->toArray();
        array_push($cooperationCrews, $manage_id);
    	
    	return $res = [
    		'basic' 	=>	$article,
    		'review'	=>	$review,
    		'opponents'	=>	$reviewOpponents,
    		'advises'	=>	$reviewAdvises,
    		'discussions'	=>	$reviewDiscussions,
    		'events'	=>	$reviewEvents,
    		'reviewArr'	=>	$reviewArr,
    		'myReview'	=>	$myReview,
    		'agreeNum'      => $agreeNum,
            'opposeNum'      => $opposeNum,
    		'neutralNum'		=> $neutralNum,
    		'cooperationCrews'	=> $cooperationCrews,
    		'active_oppose_count'	=> $active_oppose_count,
            'active_debate_count'   => $active_debate_count

    	];
    }

    // ajax获取评审的评论内容
    public function getReviewComments(Request $request,$id){
        if($id && ArticleReview::find($id)){
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = ArticleReviewOpponent::where('rid',$rid)->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = ArticleReviewAdvise::where('rid',$rid)->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewDiscussions = ArticleReviewDiscussion::where('rid',$rid)->get();
        }else{
            return 0;
        }
    }
}
