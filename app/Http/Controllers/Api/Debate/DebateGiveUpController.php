<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Publication\ArticleDebate;
use App\Home\Examination\ExamDebate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DebateGiveUpController extends Controller
{
    // 放弃攻辩
    public function debateGiveUp(Request $request){
    	$id = $request->debate_id;
    	$scope = $request->scope;
    	$reason = $request->reason;
    	$result = false;
    	$camp = $request->camp;
        $d = '';
    	if($scope==1){
    		$debate = EntryDebate::find($id);
	    	// return $request;
	    	if($camp==1 && auth('api')->user()->id==$debate->Aauthor_id && $reason){
	    		$status = '2';
				$remark = '攻方放弃本次攻辩。缘由陈述：'.$reason;
	    	}elseif($camp==2 && auth('api')->user()->id==$debate->Bauthor_id && $reason){
	    		$status = '3';
				$remark = '辩方放弃本次攻辩。缘由陈述：'.$reason;
	    	}
	    	$result = EntryDebate::debateGiveUp($id,$status,$remark);
            $d = EntryDebate::find($id);
    	}elseif($scope==2){
            $debate = ArticleDebate::find($id);
            // return $request;
            if($camp==1 && auth('api')->user()->id==$debate->Aauthor_id && $reason){
                $status = '2';
                $remark = '攻方放弃本次攻辩。缘由陈述：'.$reason;
            }elseif($camp==2 && auth('api')->user()->id==$debate->Bauthor_id && $reason){
                $status = '3';
                $remark = '辩方放弃本次攻辩。缘由陈述：'.$reason;
            }
            $result = ArticleDebate::debateGiveUp($id,$status,$remark);
            $d = ArticleDebate::find($id);
        }elseif($scope==3){
            $debate = ExamDebate::find($id);
            // return $request;
            if($camp==1 && auth('api')->user()->id==$debate->Aauthor_id && $reason){
                $status = '2';
                $remark = '攻方放弃本次攻辩。缘由陈述：'.$reason;
            }elseif($camp==2 && auth('api')->user()->id==$debate->Bauthor_id && $reason){
                $status = '3';
                $remark = '辩方放弃本次攻辩。缘由陈述：'.$reason;
            }
            $result = ExamDebate::debateGiveUp($id,$status,$remark);
            $d = ExamDebate::find($id);
        }
    	
    	return ['success'=>$result ? true:false, 'debate'  =>  $d];
    }
}
