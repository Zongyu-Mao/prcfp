<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryReview\EntryReviewAdvise;
use App\Home\Encyclopedia\EntryReview\EntryReviewEvent;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryReview\EntryReviewRecord;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\Entry;
use Input;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;


class EntryReviewController extends Controller
{
    //评审计划首页
    public function entryReview(Request $request){
        // 评审只有在协作计划活跃且词条反对均处理掉的情况下才能开启,为了避免冲突，协作计划的结束时间应该大于评审结束时间
        $id = $request->entry_id;
        $title = $request->entry_title;
        $entry = Entry::find($id);
        $cooperation = EntryCooperation::where([['eid',$id],['status','0']])->first();
        $review = EntryReview::where([['eid',$id],['status','0']])->with('getReviewRecord')->first();
        $active_oppose_count = EntryOpponent::where([['eid',$id],['status','0']])->count();
        $active_debate_count = EntryDebate::where([['eid',$id],['status','0']])->count();
        if($review){
            $rid=$review->id;
            $data_kpi = EntryReview::where([['eid',$id],['status','0']])->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = EntryReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = EntryReviewAdvise::where([['rid',$rid],['pid',0]])->with('allAdvise')->get();

            $reviewDiscussions = EntryReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();

            $reviewEvents = EntryReviewEvent::where('rid',$rid)->orderBy('created_at','desc')->limit(10)->get();
            // dd($rid);
            // dd($reviewEvents);

            $reviewRecord = EntryReviewRecord::where('review_id',$rid)->get();
            $agreeNum = EntryReviewRecord::getAgreeNum($rid);
            $opposeNum = EntryReviewRecord::getOpposeNum($rid);
            $neutralNum = EntryReviewRecord::getNeutralNum($rid);
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
    	$entryTitle = $entry->title;
    	$entryLevel = $entry->level;
        $cooperationCrews=[];
    	if($cooperation){
    		//如果存在活跃的协作计划，取得协作计划成员组
    		$cooperationCrews = $cooperation->crews()->pluck('user_id')->toArray();
    	}
    	array_push($cooperationCrews, $entry->manage_id);

    	return $res = [
    		'basic' 	=>	$entry,
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
            'active_oppose_count'   => $active_oppose_count,
    		'active_debate_count'	=> $active_debate_count

    	];
	// dd($array_encoo_crew_ids);
    	
    }

    // ajax获取评审的评论内容
    public function getReviewComments(Request $request,$id){
        if($id && EntryReview::find($id)){
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewOpponents = EntryReviewOpponent::where('rid',$id)->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewAdvises = EntryReviewAdvise::where('rid',$id)->get();
            //如果存在与本评审计划相关的反对意见，获取数据
            $reviewDiscussions = EntryReviewDiscussion::where('rid',$id)->get();
        }else{
            return 0;
        }
    }

}
