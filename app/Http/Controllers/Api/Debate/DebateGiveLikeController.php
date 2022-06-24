<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryDebate\EntryDebateStarRecord;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\ArticleDebate\ArticleDebateStarRecord;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamDebate\ExamDebateStarRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DebateGiveLikeController extends Controller
{
    //给阵营支持或不支持
    public function debateGiveLike(Request $request){
    	$id = $request->debate_id;
    	// 支持方
    	$camp = $request->camp;
    	$scope = $request->scope;
    	// 支持或反对
    	$standPoint = $request->standPoint;
    	$result = false;
    	// return $request;
    	if($scope==1){
    		$debate = EntryDebate::find($id);
	    	if(count($debate) && $debate->status=='0'){
				$record = $debate->getStars->pluck('user_id')->toArray();
				if(!in_array(auth('api')->user()->id, $record)){
					// 0支持1不支持0
	    			$star = $standPoint;
	    			// 0攻方1辩方2裁判
	    			$object = $camp;
	    			$createtime = Carbon::now();
	    			$result = EntryDebateStarRecord::giveLike($id,auth('api')->user()->id,auth('api')->user()->username,$star,$object,$createtime);
				}
	    	}
    	}elseif($scope==2){
            $debate = ArticleDebate::find($id);
            if(count($debate) && $debate->status=='0'){
                $record = $debate->getStars->pluck('user_id')->toArray();
                if(!in_array(auth('api')->user()->id, $record)){
                    // 0支持1不支持0
                    $star = $standPoint;
                    // 0攻方1辩方2裁判
                    $object = $camp;
                    $createtime = Carbon::now();
                    $result = ArticleDebateStarRecord::giveLike($id,auth('api')->user()->id,auth('api')->user()->username,$star,$object,$createtime);
                }
            }
        }elseif($scope==3){
            $debate = ExamDebate::find($id);
            if(count($debate) && $debate->status=='0'){
                $record = $debate->getStars->pluck('user_id')->toArray();
                if(!in_array(auth('api')->user()->id, $record)){
                    // 0支持1不支持0
                    $star = $standPoint;
                    // 0攻方1辩方2裁判
                    $object = $camp;
                    $createtime = Carbon::now();
                    $result = ExamDebateStarRecord::giveLike($id,auth('api')->user()->id,auth('api')->user()->username,$star,$object,$createtime);
                }
            }
        }
    	
    	return ['success'=>$result ? true:false];

    }
}
