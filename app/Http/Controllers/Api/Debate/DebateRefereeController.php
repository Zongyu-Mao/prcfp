<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Publication\ArticleDebate;
use App\Home\Examination\ExamDebate;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DebateRefereeController extends Controller
{
    //裁判在攻辩过程中的分析
    public function debateAnalyse(Request $request){
        $id = $request->debate_id;
        $scope = $request->scope;
        $analyse = $request->analyse;
        $analyseTime = Carbon::now();
        $d = '';
        if($scope==1){
        	$debate = EntryDebate::find($id);
	    	if($debate && $debate->status==0 && auth('api')->user()->id == $debate->referee_id){
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
				//写入辩方立论和开篇陈词
				$result = EntryDebate::debateAnalyse($id,$analyse,$analyseTime);
				if($result){
					$d = EntryDebate::find($id);
				}
	    	}
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
	    	if($debate && $debate->status==0 && auth('api')->user()->id == $debate->referee_id){
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
				//写入辩方立论和开篇陈词
				$result = ArticleDebate::debateAnalyse($id,$analyse,$analyseTime);
				if($result){
					$d = ArticleDebate::find($id);
				}
	    	}
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
	    	if($debate && $debate->status==0 && auth('api')->user()->id == $debate->referee_id){
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
				//写入辩方立论和开篇陈词
				$result = ExamDebate::debateAnalyse($id,$analyse,$analyseTime);
				if($result){
					$d = ExamDebate::find($id);
				}
	    	}
        }
        
        return ['success'=>$result ? true:false,'debate'=>$d];
    }

    //辩方写入总结陈词后，裁判总结
    public function debate_summary(Request $request){
        $id = $request->debate_id;
        $result = false;
        $scope = $request->scope;
        $author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $checkTime = Carbon::now();
        $d = '';
        $check = 0;
        // return $request;
        if($scope==1){
        	$debate = EntryDebate::find($id);
	        if($debate && $debate->status==0){
	            
	            $author_referee_id = $debate->referee_id;
	            $author_referee = $debate->referee;
	            // $summaryCreateTime = Carbon::now();
	            // 判断裁判总结的时间是否在期限之前
	            
	            if($checkTime <= $debate->deadline){
	                $check = 1;
	            }elseif($checkTime > $debate->deadline){
	                $newDebate = EntryDebate::find($id);
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = '1';
	                $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算！';
	                $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                if($Adata >= $Bdata){
	                    $victory = 1;
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。攻方['.$newDebate->Aauthor.']胜出！';
	                }else{
	                    $victory = 2;
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。辩方['.$newDebate->Bauthor.']胜出！';
	                }
	                EntryDebate::debateTimeOutByRefereeClear($id,$status,$remark,$victory);
	            }
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
	            if($author_id==$author_referee_id){
	                $summary = $request->summary;
	                $status = 1;
	                $victory = $request->victory;
	                if($victory == '1'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，攻方['.$debate->Aauthor.']胜出。';
	                }elseif($victory == '2'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，辩方['.$debate->Bauthor.']胜出。';
	                }
	                //更新辩论表
	                $result = EntryDebate::debate_summary($id,$summary,$status,$remark,$victory);
	            }
	            $d = EntryDebate::find($id);
	        }
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
	        if($debate && $debate->status==0){
	            
	            $author_referee_id = $debate->referee_id;
	            $author_referee = $debate->referee;
	            // $summaryCreateTime = Carbon::now();
	            // 判断裁判总结的时间是否在期限之前
	            
	            if($checkTime <= $debate->deadline){
	                $check = 1;
	            }elseif($checkTime > $debate->deadline){
	                $newDebate = ArticleDebate::find($id);
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 1;
	                $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算！';
	                $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                if($Adata >= $Bdata){
	                    $victory = 1;
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。攻方['.$newDebate->Aauthor.']胜出！';
	                }else{
	                    $victory = 2;
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。辩方['.$newDebate->Bauthor.']胜出！';
	                }
	                ArticleDebate::debateTimeOutByRefereeClear($id,$status,$remark,$victory);
	            }
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
	            if($author_id==$author_referee_id){
	                $summary = $request->summary;
	                $status = '1';
	                $victory = $request->victory;
	                if($victory == '1'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，攻方['.$debate->Aauthor.']胜出。';
	                }elseif($victory == '2'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，辩方['.$debate->Bauthor.']胜出。';
	                }
	                //更新辩论表
	                $result = ArticleDebate::debate_summary($id,$summary,$status,$remark,$victory);

	            }
	            $d = ArticleDebate::find($id);
	        }
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
	        if($debate && $debate->status==0){
	            $author_referee_id = $debate->referee_id;
	            $author_referee = $debate->referee;
	            // $summaryCreateTime = Carbon::now();
	            // 判断裁判总结的时间是否在期限之前
	            
	            if($checkTime <= $debate->deadline){
	                $check = '1';
	            }elseif($checkTime > $debate->deadline){
	                $newDebate = ExamDebate::find($id);
	                $check = '2';
	                // 由于辩方原因超时，状态变更为3；
	                $status = '1';
	                $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算！';
	                $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                if($Adata >= $Bdata){
	                    $victory = '1';
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。攻方['.$newDebate->Aauthor.']胜出！';
	                }else{
	                    $victory = '2';
	                    $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。辩方['.$newDebate->Bauthor.']胜出！';
	                }
	                ExamDebate::debateTimeOutByRefereeClear($id,$status,$remark,$victory);
	            }
	            // 这里不需要裁判总结事件，等与辩论的最后更新时间
	            if($author_id==$author_referee_id){
	                $summary = $request->summary;
	                $status = '1';
	                $victory = $request->victory;
	                if($victory == '1'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，攻方['.$debate->Aauthor.']胜出。';
	                }elseif($victory == '2'){
	                    $remark = '攻辩流程已经结束，经裁判['.$debate->referee.']裁定，辩方['.$debate->Bauthor.']胜出。';
	                }
	                //更新辩论表
	                $result = ExamDebate::debate_summary($id,$summary,$status,$remark,$victory);

	            }
	            $d = ExamDebate::find($id);
	        }

        }
        
        return ['success'=>$result ? true:false,'check'=>$check,'debate'=>$d];
    }

    //成为裁判
    public function asTheReferee(Request $request){
        // 检查辩论状态，如果辩论正常，可以允许裁判加入
        $id = $request->debate_id;
        $result = false;
        $scope = $request->scope;
        // return $request;
        $user = auth('api')->user();
		$user_grow_value = $user->grow_value;
		$d = '';
		if($scope==1){
			$debate = EntryDebate::find($id);
			if($user_grow_value>='5000' && $debate->status==0 && $debate->referee_id==NULL){
				$referee = $user->username;
				$referee_id = $user->id;
				// 更新裁判为当前用户
				$result = EntryDebate::asReferee($id,$referee,$referee_id);
	        	// 暂时不考虑加分
	        	// $result2 = User::expAndGrowValue($author_id,100,100);
	        	if($result) {
	        		$d = EntryDebate::find($id);
	        	}
			}
		}elseif($scope==2){
			$debate = ArticleDebate::find($id);
			if($user_grow_value>='5000' && $debate->status==0 && $debate->referee_id==NULL){
				$referee = $user->username;
				$referee_id = $user->id;
				// 更新裁判为当前用户
				$result = ArticleDebate::asReferee($id,$referee,$referee_id);
	        	// 暂时不考虑加分
	        	// $result2 = User::expAndGrowValue($author_id,100,100);
	        	if($result) {
	        		$d = EntryDebate::find($id);
	        	}
			}
		}elseif($scope==3){
			$debate = ExamDebate::find($id);
			if($user_grow_value>='5000' && $debate->status==0 && $debate->referee_id==NULL){
				$referee = $user->username;
				$referee_id = $user->id;
				// 更新裁判为当前用户
				$result = ExamDebate::asReferee($id,$referee,$referee_id);
	        	// 暂时不考虑加分
	        	// $result2 = User::expAndGrowValue($author_id,100,100);
	        	if($result) {
	        		$d = EntryDebate::find($id);
	        	}
			}
		}
    	return ['success'=>$result ? true:false,'debate'=>$d];
    }
}
