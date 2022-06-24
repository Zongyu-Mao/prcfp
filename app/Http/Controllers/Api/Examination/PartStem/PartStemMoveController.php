<?php

namespace App\Http\Controllers\Api\Examination\PartStem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPartStem;
use App\Home\Examination\Exam\ExamQuestion;
use App\Events\Examination\Exam\PartStem\PartStemMoveForwardEvent;
use App\Events\Examination\Exam\PartStem\PartStemMoveBackwardEvent;
use App\Events\Examination\Exam\PartStem\PartStemDeletedEvent;

class PartStemMoveController extends Controller
{
    //材料的前移
    public function partStemMove(Request $request){
    	$id = $request->stem_id;
    	$move = $request->move;
    	$stem = ExamPartStem::find($id);
    	$stems = '';
    	if($move == 1){
    		// 前移材料，如果材料已经在首位，即stem的sort是1，且qid的sort是1，如果不是且前面的question不是材料题，材料和材料题与前面的question交换位置，如果是材料题，需要两方材料题全部交换位置；
	    	if($stem->sort == '1' && $stem->qid == '0'){
	    		$result == '0';
	    	}elseif($stem->sort > '1' && $stem->qid == '0'){
	    		$stemSort = $stem->sort;
	    		ExamPartStem::where([['exam_id',$stem->exam_id],['sort',$stem->sort-1]])->update(['sort'=>$stem->sort]);
	            $result = ExamPartStem::where('id',$stem->id)->update(['sort'=>$stem->sort-1]);
	    	}elseif($stem->sort >= '1' && $stem->qid > '0'){
	    		$question = ExamQuestion::find($stem->qid);
	    		if($stem->sort == '1' && $question->sort == '1'){
	    			$result = '0';
	    		}elseif($stem->sort >= '1' && $question->sort > '1'){
	    			// 这时候需要判断stem前面是question还是材料
	    			$judge = ExamQuestion::where([['exam_id',$stem->exam_id],['sort',$question->sort-1]])->first();
	    			if(count($judge) && $judge->partStem){
	    				// 临近的question有材料
	    				$judgeStem = ExamPartStem::find($judge->partStem);
	    				$forwardStemQuestions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$judge->partStem]])->get();
	    				$moveStemQuestions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$question->partStem]])->get();
	    				$forward = count($forwardStemQuestions);
	    				$backward = count($moveStemQuestions);
	    				// 交换两方材料的问题sort,原来question的sort要加上移动的问题总数
	    				foreach($forwardStemQuestions as $forwardQuestion){
	    					ExamQuestion::where('id',$forwardQuestion->id)->update(['sort'=>$forwardQuestion->sort+$backward]);
	    				}
	    				// 要移动的问题要减去原来question的问题数
	    				foreach($moveStemQuestions as $moveQuestion){
	    					ExamQuestion::where('id',$moveQuestion->id)->update(['sort'=>$moveQuestion->sort-$forward]);
	    				}
	    				// 交换两方材料的sort
	    				$result = ExamPartStem::where('id',$stem->id)->update(['sort'=>$judgeStem->sort]);
	    				ExamPartStem::where('id',$judgeStem->id)->update(['sort'=>$stem->sort]);
	    			}elseif(count($judge) && !$judge->partStem){
	    				// 临近的question没有材料,只需要移动的question的sort-1，被置换的question的sort+$backward;partStem的sort不用改变
	    				$moveStemQuestions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$question->partStem]])->get();
	    				$backward = count($moveStemQuestions);
	    				$result = ExamQuestion::where('id',$judge->id)->update(['sort'=>$judge->sort+$backward]);
	    				foreach($moveStemQuestions as $moveQuestion){
	    					ExamQuestion::where('id',$moveQuestion->id)->update(['sort'=>$moveQuestion->sort-1]);
	    				}
	    			}else{
	                    $result = '0';
	                }
	    		}
	    	}
	    	$result =  $result ? '1':'0';
	        if($result){
	            Event(new PartStemMoveForwardEvent($stem));
	        }
    	}elseif ($move==2) {
    		// move backward
    		if($stem->sort && $stem->qid){
	    		// 移动的前提是有sort，如果材料带了问题
	    		$question = ExamQuestion::find($stem->qid);
	    		// 找到最后一位
	    		$maxStemSort = max(ExamPartStem::where('exam_id',$stem->exam_id)->pluck('sort')->toArray());
	    		$maxStemJudgeSort = max(ExamQuestion::where('partStem',$stem->id)->pluck('sort')->toArray());
	    		$maxQuestionSort = max(ExamQuestion::where('exam_id',$stem->exam_id)->pluck('sort')->toArray());
	    		if($question->sort == $maxQuestionSort || $maxStemJudgeSort == $maxQuestionSort){
	    			// 如果stem的sort最大，question的sort不一定最大，stem的sort其实并没有太大的意义
	    			// 如果question的sort是最大的，不用后移，如果材料中的问题有最大sort，也不用后移
	    			$result = '0';
	    		}else{
	    			// 其余情况均是需要材料往后移动的，因为除了上面的情况，材料及其问题都不会在最后
	    			// 这时只需要分材料后面是材料还是非材料题
	    			$judgeQuestion = ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort+1]])->first();
	    			if($judgeQuestion->partStem){
	    				$judgeStem = ExamPartStem::find($judgeQuestion->partStem);
	    				// 由于有材料，所以要互相交换材料和问题的sort，这是被动材料所带问题数
	    				$backwardStemQuestions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$judgeQuestion->partStem]])->get();
	    				// 这是主动材料所带问题数
	    				$moveStemQuestions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$question->partStem]])->get();
	    				$forward = count($backwardStemQuestions);
	    				$backward = count($moveStemQuestions);
	    				// 交换两方材料的问题sort,原来被动的question的sort要减去移动的问题总数
	    				foreach($backwardStemQuestions as $backwardQuestion){
	    					ExamQuestion::where('id',$backwardQuestion->id)->update(['sort'=>$backwardQuestion->sort-$backward]);
	    				}
	    				// 要移动的问题要加上原来被动question的问题数
	    				foreach($moveStemQuestions as $moveQuestion){
	    					ExamQuestion::where('id',$moveQuestion->id)->update(['sort'=>$moveQuestion->sort+$forward]);
	    				}
	    				// 交换两方材料的sort
	    				$result = ExamPartStem::where('id',$stem->id)->update(['sort'=>$judgeStem->sort]);
	    				ExamPartStem::where('id',$judgeStem->id)->update(['sort'=>$stem->sort]);
	    			}
	    		}
	    	}elseif($stem->qid == '0'){
	            // 材料是没有问题的
	            $maxStemSort = max(ExamPartStem::where('exam_id',$stem->exam_id)->pluck('sort')->toArray());
	            if($stem->sort < $maxStemSort){
	                if(ExamPartStem::where([['exam_id',$stem->exam_id],['sort',$stem->sort+1]])->exists()){
	                    ExamPartStem::where([['exam_id',$stem->exam_id],['sort',$stem->sort+1]])->update(['sort'=>$stem->sort]);
	                }
	                $result = ExamPartStem::where('id',$stem->id)->update(['sort'=>$stem->sort+1]);
	            }else{
	                $result = '0';
	            }
	        }else{
	            $result = '0';
	        }
	    	$result =  $result ? '1':'0';
	        if($result){
	            Event(new PartStemMoveBackwardEvent($stem));
	        }
    	}elseif ($move==3) {
    		// delete exam stem
    		$questions = ExamQuestion::where([['exam_id',$stem->exam_id],['partStem',$id]])->get();
	    	$result = '0';
	    	if($stem->qid || count($questions)){
	    		foreach($questions as $question){
	    			$res = ExamQuestion::where('id',$question->id)->update(['partStem'=>'0']);
	    			$res ? '1':'0';
	    			$result += $res;
	    		}
	    	}
	        $changeStems = ExamPartStem::where([['exam_id',$stem->exam_id],['sort','>',$stem->sort]])->get();
	        if(count($changeStems)){
	            foreach($changeStems as $ste){
	                ExamPartStem::where('id',$ste->id)->update(['sort'=>$ste->sort-1]);
	            }
	        }
	    	$result = ExamPartStem::where('id',$id)->delete();
	    	$result =  $result ? '1':'0';
	        if($result){
	            Event(new PartStemDeletedEvent($stem));
	        }
    	}
    	if($result)$stems = ExamPartStem::where('exam_id',$stem->exam_id)->orderBy('sort','asc')->get();
		return ['success'=>$result ? true:false,'stems'=>$stems];
    }
}
