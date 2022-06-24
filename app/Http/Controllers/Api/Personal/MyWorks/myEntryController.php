<?php

namespace App\Http\Controllers\Api\Personal\MyWorks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use Illuminate\Support\Facades\Auth;

class myEntryController extends Controller
{
    //展示我的词条
    public function myEntries(Request $request){
        $user = auth('api')->user();
    	$user_id = $user->id;
    	// 我的自管理词条
    	$manageEntries = Entry::where('manage_id',$user_id)->orderBy('created_at','desc')->get();
        $manageCooeprationIds = array_filter($manageEntries->pluck('cooperation_id')->toArray());
        // 我的自管理协作计划
        $manageCooperations = EntryCooperation::whereIn('id',$manageCooeprationIds)->with('getEntry')->orderBy('created_at','desc')->get();
        // 我的普通协作
        $cooperationIds = EntryCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();
        $normalCooperations = EntryCooperation::whereIn('id',$cooperationIds)->with('getEntry')->orderBy('created_at','desc')->get();
        // 我的求助
        $myResorts = EntryResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->get();
        // 我的评审
        $myReviews = EntryReview::where('initiate_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->get();
        
        // 我的攻辩
        $myDebates = EntryDebate::where('Aauthor_id',$user_id)->orWhere('Bauthor_id',$user_id)->orWhere('referee_id',$user_id)->with('getContent')->orderBy('created_at','desc')->get();

        // 我的反对
        $myOpponents = EntryOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->get();
        // 我的建议
        $myAdvises = EntryAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->get();
        
        return $data = array(
        	'entries' => $manageEntries,
        	'm_cooperations' => $manageCooperations,
        	'n_cooperations' => $normalCooperations,
        	'resorts' => $myResorts,
        	'reviews' => $myReviews,
        	'debates' => $myDebates,
        	'opponents' => $myOpponents,
        	'advises' => $myAdvises
        );
    }
}
