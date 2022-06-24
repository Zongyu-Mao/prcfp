<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DebateCreateController extends Controller
{
    // 攻辩计划创建
    public function debate_create(Request $request){
    	// return $request;
    	$opponent_id = $request->opponent_id;
    	$scope = $request->scope;
    	$id = $request->id;
    	$cid = $request->cid;
    	$type = $request->type;
    	$result = '';
		$title = $request->title;
		$AOS = $request->content;
		$deadline = Carbon::now()->addDays(30);
		$Aauthor_id = auth('api')->user()->id;
        $Aauthor = auth('api')->user()->username;
        $remark = '开篇陈词已经发布，攻辩开始，等待辩方陈词阶段。';
        if($scope==1){
	        if($opponent_id && !EntryDebate::where([['type',$type],['type_id',$opponent_id],['status','0']])->exists()){

				if($type == 1){
		            $reviewOpponent = EntryReviewOpponent::find($opponent_id);
		            $Bauthor_id = $reviewOpponent->author_id;
		            $Bauthor = $reviewOpponent->author;
		            $result = EntryDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}elseif($type == 2){
					$opponent = EntryOpponent::find($opponent_id);
		            $Bauthor_id = $opponent->author_id;
		            $Bauthor = $opponent->author;
	                $result = EntryDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}				
	    	}
        }elseif($scope==2){
        	if($opponent_id && !ArticleDebate::where([['type',$type],['type_id',$opponent_id],['status','0']])->exists()){
				if($type == 1){
		            $reviewOpponent = ArticleReviewOpponent::find($opponent_id);
		            $Bauthor_id = $reviewOpponent->author_id;
		            $Bauthor = $reviewOpponent->author;
		            $result = ArticleDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}elseif($type == 2){
					$opponent = ArticleOpponent::find($opponent_id);
		            $Bauthor_id = $opponent->author_id;
		            $Bauthor = $opponent->author;
	                $result = ArticleDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}				
	    	}
        }elseif($scope==3){
        	if($opponent_id && !ExamDebate::where([['type',$type],['type_id',$opponent_id],['status','0']])->exists()){
				if($type == 1){
		            $reviewOpponent = ExamReviewOpponent::find($opponent_id);
		            $Bauthor_id = $reviewOpponent->author_id;
		            $Bauthor = $reviewOpponent->author;
		            $result = ExamDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}elseif($type == 2){
					$opponent = ExamOpponent::find($opponent_id);
		            $Bauthor_id = $opponent->author_id;
		            $Bauthor = $opponent->author;
	                $result = ExamDebate::debate_create($id,$cid,$type,$opponent_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark);
				}				
	    	}
        }
    	
    	return ['success'=>$result ? true:false,'debate'=>$result];
    }

    // 攻方开启攻辩后，辩方写入开篇和立论
    public function BOS_create(Request $request){
    	// return $request;
    	$id = $request->debate_id;
    	$scope = $request->scope;
    	$author_id = auth('api')->user()->id;
        $result = false;
        $r = false;
        $d = '';
        $remark = '';
        $check = 0;
        if($scope==1){
        	$debate = EntryDebate::find($id);
	    	if($debate){
	    		$author_expected_id = $debate->Bauthor_id;
	            $checkTime = Carbon::now()->subDays('5');
	            $BOStime = Carbon::now();
	            if($checkTime <= $debate->created_at){
	                $check = 1;
	            }elseif($checkTime > $debate->created_at){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }

	            // 这里取消check的限制，仍然写入内容，但是仍然改变status,但是debate的status
	    		if($author_id == $author_expected_id && $debate->status==0){
	                $BOS = $request->content;
	                $remark =  ($check == 1 ? '辩方开篇陈词已经发布，等待攻方自由辩论阶段。':'攻辩由于辩方回应[开篇陈词]超时关闭！');
	                if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	                //写入辩方立论和开篇陈词
	                $result = EntryDebate::debate_bopening($id,$BOS,$BOStime,$remark);
	                if($result){
	                	$d = EntryDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
	    	if($debate){
	    		$author_expected_id = $debate->Bauthor_id;
	            $checkTime = Carbon::now()->subDays('5');
	            $BOStime = Carbon::now();
	            if($checkTime <= $debate->created_at){
	                $check = 1;
	            }elseif($checkTime > $debate->created_at){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id == $author_expected_id && $debate->status==0){
	                $BOS = $request->content;
	                $remark =  ($check == 1 ? '辩方开篇陈词已经发布，等待攻方自由辩论阶段。':'攻辩由于辩方回应[开篇陈词]超时关闭！');
	                if($check==2)$r = ArticleDebate::debateTimeOutClose($id,$status,$remark);
	                //写入辩方立论和开篇陈词
	                $result = ArticleDebate::debate_bopening($id,$BOS,$BOStime,$remark);
	                if($result){
	                	$d = ArticleDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
	    	if($debate){
	    		$author_expected_id = $debate->Bauthor_id;
	            $checkTime = Carbon::now()->subDays(5);
	            $BOStime = Carbon::now();
	            if($checkTime <= $debate->created_at){
	                $check = 1;
	            }elseif($checkTime > $debate->created_at){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id == $author_expected_id && $debate->status==0){
	                $BOS = $request->content;
	                $remark =  ($check == 1 ? '辩方开篇陈词已经发布，等待攻方自由辩论阶段。':'攻辩由于辩方回应[开篇陈词]超时关闭！');
	                if($check==2)$r = ExamDebate::debateTimeOutClose($id,$status,$remark);
	                //写入辩方立论和开篇陈词
	                $result = ExamDebate::debate_bopening($id,$BOS,$BOStime,$remark);
	                if($result){
	                	$d = ExamDebate::find($id);
	                }
	    		}
	    	}
        }
        
    	return ['success'=>$result ? true:false,'check'=>$check,'remark'=>$remark,'debate'=>$d];
    }

    //辩方写入开篇陈词后，攻方进入自由辩论阶段
    public function AFD_create(Request $request){
    	$id = $request->debate_id;
    	$scope = $request->scope;
        $result = false;
        $author_id = auth('api')->user()->id;
        
	    $AFDtime = Carbon::now();
	    $d = '';
        $remark = '';
        $check = 0;
        // return $request;
        if($scope==1){
        	$debate = EntryDebate::find($id);
	    	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->BOScreateTime ){
	                $check = 1;
	            }elseif($checkTime > $debate->BOScreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 2;
	            }
	            // return $check;
	    		if($author_id==$author_expected_id){
	    			$AFD = $request->content;
	    			$remark =  ($check == 1 ? '攻方自由辩论已经发布，等待辩方自由辩论阶段。':'攻辩由于攻方回应[自由辩论]超时关闭！');
	    			if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = EntryDebate::debate_AFreeDebate($id,$AFD,$AFDtime,$remark);
	    			if($result){
	                	$d = EntryDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
	    	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->BOScreateTime ){
	                $check = 1;
	            }elseif($checkTime > $debate->BOScreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 2;
	            }
	            // return $check;
	    		if($author_id==$author_expected_id){
	    			$AFD = $request->content;
	                $remark =  ($check == 1 ? '攻方自由辩论已经发布，等待辩方自由辩论阶段。':'攻辩由于攻方回应[自由辩论]超时关闭！');
	                if($check==2)$r = ArticleDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ArticleDebate::debate_AFreeDebate($id,$AFD,$AFDtime,$remark);
	    			if($result){
	                	$d = ArticleDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
	    	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->BOScreateTime ){
	                $check = 1;
	            }elseif($checkTime > $debate->BOScreateTime){
	                $check = 2;
	                $status = 2;
	            }
	            // return $check;
	    		if($author_id==$author_expected_id && $debate->status==0){
	    			$AFD = $request->content;
	                $remark =  ($check == 1 ? '攻方自由辩论已经发布，等待辩方自由辩论阶段。':'攻辩由于攻方回应[自由辩论]超时关闭！');
	                if($check==2)$r = ExamDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ExamDebate::debate_AFreeDebate($id,$AFD,$AFDtime,$remark);
	    			if($result){
	                	$d = ExamDebate::find($id);
	                }
	    		}
	    	}
        }
        
    	return ['success'=>$result ? true:false,'check'=>$check,'remark'=>$remark,'debate'=>$d];
    }
    //攻方写入自由辩论后，辩方进入自由辩论阶段
    public function BFD_create(Request $request){
    	$id = $request->debate_id;
    	$scope = $request->scope;
        $author_id = auth('api')->user()->id;
        $result = false;
        $d = '';
        $remark = '';
        $check = 0;
        // return $request;
        if($scope==1){
        	$debate = EntryDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$BFDtime = Carbon::now();
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->AFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->AFDcreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BFD = $request->content;
	    			$remark =  ($check == 1 ? '辩方自由辩论已经发布，等待攻方总结陈词阶段。':'攻辩由于辩方回应[自由辩论]超时关闭！');
	    			if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = EntryDebate::debate_BFreeDebate($id,$BFD,$BFDtime,$remark);
	    			if($result){
	                	$d = EntryDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$BFDtime = Carbon::now();
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->AFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->AFDcreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BFD = $request->content;
	                $remark =  ($check == 1 ? '辩方自由辩论已经发布，等待攻方总结陈词阶段。':'攻辩由于辩方回应[自由辩论]超时关闭！');
	                if($check==2)$r = ArticleDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ArticleDebate::debate_BFreeDebate($id,$BFD,$BFDtime,$remark);
	    			if($result){
	                	$d = ArticleDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$BFDtime = Carbon::now();
	            $checkTime = Carbon::now()->subDays(5);
	            if($checkTime <= $debate->AFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->AFDcreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BFD = $request->content;
	                $remark =  ($check == 1 ? '辩方自由辩论已经发布，等待攻方总结陈词阶段。':'攻辩由于辩方回应[自由辩论]超时关闭！');
	                if($check==2)$r = ExamDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ExamDebate::debate_BFreeDebate($id,$BFD,$BFDtime,$remark);
	    			if($result){
	                	$d = ExamDebate::find($id);
	                }
	    		}
	    	}
        }
    	
    	return ['success'=>$result ? true:false,'check'=>$check,'remark'=>$remark,'debate'=>$d];
    }

    //辩方写入自由辩论后，攻方进入总结陈词阶段
    public function ACS_create(Request $request){
    	$id = $request->debate_id;
    	$scope = $request->scope;
        $author_id = auth('api')->user()->id;
        $ACStime = Carbon::now();
	    $checkTime = Carbon::now()->subDays('5');
        $result = false;
        $d = '';
        $remark = '';
        $check = 0;
        // return $request;
        if($scope==1){
        	$debate = EntryDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            if($checkTime <= $debate->BFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->BFDcreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 2;
	            }
	    		if($author_id==$author_expected_id){
	    			$ACS = $request->content;
	    			$remark =  ($check == 1 ? '攻方总结陈词已经发布，等待辩方总结陈词阶段。':'攻辩由于攻方回应[总结陈词]超时关闭！');
	                if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = EntryDebate::debate_AClosingStatement($id,$ACS,$ACStime,$remark);
	    			if($result){
	                	$d = EntryDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==2){
        	$debate = ArticleDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            if($checkTime <= $debate->BFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->BFDcreateTime){
	                $check = 2;
	                $status = 2;
	            }
	    		if($author_id==$author_expected_id){
	    			$ACS = $request->content;
	                $remark =  ($check == 1 ? '攻方总结陈词已经发布，等待辩方总结陈词阶段。':'攻辩由于攻方回应[总结陈词]超时关闭！');
	                if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ArticleDebate::debateTimeOutClose($id,$status,$remark);
	    			if($result){
	                	$d = ArticleDebate::find($id);
	                }
	    		}
	    	}
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Aauthor_id;
	            if($checkTime <= $debate->BFDcreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->BFDcreateTime){
	                $check = 2;
	                $status = 2;
	            }
	    		if($author_id==$author_expected_id){
	    			$ACS = $request->content;
	                $remark =  ($check == 1 ? '攻方总结陈词已经发布，等待辩方总结陈词阶段。':'攻辩由于攻方回应[总结陈词]超时关闭！');
	                if($check==2)$r = ExamDebate::debateTimeOutClose($id,$status,$remark);
	    			//写入辩方立论和开篇陈词
	    			$result = ExamDebate::debate_AClosingStatement($id,$ACS,$ACStime,$remark);
	    			if($result){
	                	$d = ExamDebate::find($id);
	                }
	    		}
	    	}
        }
    	
    	return ['success'=>$result ? true:false,'check'=>$check,'remark'=>$remark,'debate'=>$d];
    }

    //攻方写入总结陈词后，辩方进入总结陈词阶段
    public function BCS_create(Request $request){
    	$id = $request->debate_id;
    	$scope = $request->scope;
        $author_id = auth('api')->user()->id;
        $BCStime = Carbon::now();
	    $checkTime = Carbon::now()->subDays('5');
        $result = false;
        $d = '';
        $remark = '';
        $check = 0;
        // return $request;
        if($scope==1){
        	$debate = EntryDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$title = $debate->title;
	            if($checkTime <= $debate->ACScreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->ACScreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BCS = $request->content;
	    			//写入辩方立论和开篇陈词
	                if($check==1) {
	                	if($debate->referee_id){
		                    $remark = '辩方总结陈词已经发布，攻辩选手环节已经结束。等待裁判['.$debate->referee.']总结和系统结算。';
		                }else{
		                    $remark = '辩方总结陈词已经发布，进入结算。';
		                } 
	                } else if($check==2) {
	                	$remark = '攻辩由于辩方回应[总结陈词]超时关闭！';
	                }
	                if($check==2)$r = EntryDebate::debateTimeOutClose($id,$status,$remark);
	    			$result = EntryDebate::debate_BClosingStatement($id,$BCS,$BCStime,$remark);
	    			// 这里暂时结算一下，后面再考虑如何结算
	                if($result && !$debate->referee_id){
	                    $newDebate = EntryDebate::find($id);
	                    $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                    $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                    if($Adata >= $Bdata){
	                        $victory = 1;
	                        $remark = '本次攻辩由系统自动结算。攻方['.$newDebate->Aauthor.']胜利！';
	                    }else{
	                        $victory = 2;
	                        $remark = '本次攻辩由系统自动结算。辩方['.$newDebate->Bauthor.']胜利！';
	                    }
	                    $status = 1;
	                    EntryDebate::debate_automatically_clear($id,$status,$remark,$victory);

	                }
	    		}
	    		if($result){
                	$d = EntryDebate::find($id);
                }
	        }
    	}elseif($scope==2){
        	$debate = ArticleDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$title = $debate->title;
	            if($checkTime <= $debate->ACScreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->ACScreateTime){
	                $check = 2;
	                // 由于辩方原因超时，状态变更为3；
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BCS = $request->content;
	    			//写入辩方立论和开篇陈词
	                if($check==1) {
	                	if($debate->referee_id){
		                    $remark = '辩方总结陈词已经发布，攻辩选手环节已经结束。等待裁判['.$debate->referee.']总结和系统结算。';
		                }else{
		                    $remark = '辩方总结陈词已经发布，进入结算。';
		                } 
	                } else if($check==2) {
	                	$remark = '攻辩由于辩方回应[总结陈词]超时关闭！';
	                }
	                if($check==2)$r = ArticleDebate::debateTimeOutClose($id,$status,$remark);
	    			$result = ArticleDebate::debate_BClosingStatement($id,$BCS,$BCStime,$remark);
	    			// 这里暂时结算一下，后面再考虑如何结算
	                if($result && $debate->referee_id == NULL){
	                    $newDebate = ArticleDebate::find($id);
	                    $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                    $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                    if($Adata >= $Bdata){
	                        $victory = 1;
	                        $remark = '本次攻辩由系统自动结算。攻方['.$newDebate->Aauthor.']胜利！';
	                    }else{
	                        $victory = 2;
	                        $remark = '本次攻辩由系统自动结算。辩方['.$newDebate->Bauthor.']胜利！';
	                    }
	                    $status = 1;
	                    ArticleDebate::debate_automatically_clear($id,$status,$remark,$victory);
	                }
	    		}
	    		if($result){
                	$d = ArticleDebate::find($id);
                }
	        }
        }elseif($scope==3){
        	$debate = ExamDebate::find($id);
        	if($debate && $debate->status==0){
	    		$author_expected_id = $debate->Bauthor_id;
	    		$title = $debate->title;
	            if($checkTime <= $debate->ACScreateTime){
	                $check = 1;
	            }elseif($checkTime > $debate->ACScreateTime){
	                $check = 2;
	                $status = 3;
	            }
	    		if($author_id==$author_expected_id){
	    			$BCS = $request->content;
	    			//写入辩方立论和开篇陈词
	                if($check==1) {
	                	if($debate->referee_id){
		                    $remark = '辩方总结陈词已经发布，攻辩选手环节已经结束。等待裁判['.$debate->referee.']总结和系统结算。';
		                }else{
		                    $remark = '辩方总结陈词已经发布，进入结算。';
		                } 
	                } else if($check==2) {
	                	$remark = '攻辩由于辩方回应[总结陈词]超时关闭！';
	                }
	                if($check==2)$r = ExamDebate::debateTimeOutClose($id,$status,$remark);
	    			$result = ExamDebate::debate_BClosingStatement($id,$BCS,$BCStime,$remark);
	    			// 这里暂时结算一下，后面再考虑如何结算
	                if($result && $debate->referee_id == NULL){
	                    $newDebate = ExamDebate::find($id);
	                    $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
	                    $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
	                    if($Adata >= $Bdata){
	                        $victory = '1';
	                        $remark = '本次攻辩由系统自动结算。攻方['.$newDebate->Aauthor.']胜利！';
	                    }else{
	                        $victory = '2';
	                        $remark = '本次攻辩由系统自动结算。辩方['.$newDebate->Bauthor.']胜利！';
	                    }
	                    $status = '1';
	                    ExamDebate::debate_automatically_clear($id,$status,$remark,$victory);
	                }
	    		}
	    		if($result){
                	$d = ExamDebate::find($id);
                }
	        }
        }
    	return ['success'=>$result ? true:false,'check'=>$check,'remark'=>$remark,'debate'=>$d];
    }
}
