<?php

namespace App\Http\Controllers\Api\Examination\Question;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPartStem;
use App\Home\Examination\Exam\ExamQuestion;
use App\Home\Examination\Exam\Question\ExamQuestionOption;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionMoveForwardEvent;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionMoveBackwardEvent;
use App\Events\Examination\Exam\QuestionMethod\ExamQuestionDeletedEvent;

class QuestionMoveController extends Controller
{
    // 试题的移动规则：不能越过材料移动
    //试题的前移操作
    public function questionMove(Request $request){
    	$id = $request->question_id;
    	$move = $request->move;
    	$question = ExamQuestion::find($id);
    	if($move==1){
    		if($question->sort == 1){
	    		$result = '0';
	    	}elseif($question->partStem && $question->sort > 1){
	    		// 如果是材料题
	    		$stem = ExamPartStem::find($question->partStem);
	    		if($stem->qid == $id){
	    			if(!ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort-1]])->exists()){
	    				$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort-1]);
	    			}else{
	    				$result = '0';
	    			}
	    		}else{
	    			$judgeQuestion = ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort-1]])->first();
	    			if(ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort-1]])->exists()){
	    				ExamQuestion::where('id',$judgeQuestion->id)->update(['sort'=>$question->sort]);
	    			}
	    			$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort-1]);
	    			if($judgeQuestion && ($stem->qid == $judgeQuestion->id)){
	    				ExamPartStem::where('id',$stem->id)->update(['qid'=>$id]);
	    			}
	    		}
	    	}else{
	    		// 试题不是材料题，且sort大于1，注意不能越过材料
	    		if(ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort-1]])->exists()){
	    			$judgeQuestion = ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort-1]])->first();
	    			if($judgeQuestion->partStem){
		    			$result = '0';
		    		}else{
		    			$result = ExamQuestion::where('id',$id)->update(['sort'=>$judgeQuestion->sort]);
		    			if(ExamQuestion::where('id',$judgeQuestion->id)->exists()){
		    				ExamQuestion::where('id',$judgeQuestion->id)->update(['sort'=>$question->sort]);
		    			}
		    		}
	    		}else{
	    			// 如果不存在为sort-1的题目，直接改变sort即可，至于大于sort的，改动影响较大，判断较多因此暂时不做改动
	    			$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort-1]);
	    		}
	    		
	    	}
	    	$result =  $result ? '1':'0';
	        if($result){
	            Event(new ExamQuestionMoveForwardEvent($question));
	        }
    	}elseif ($move==2) {
    		// 本试卷试题的最大sort
			$maxQuestionSort = max(ExamQuestion::where('exam_id',$question->exam_id)->pluck('sort')->toArray());
			if($question->sort == $maxQuestionSort){
				$result = '0';
			}elseif($question->sort < $maxQuestionSort){
				if($question->partStem){
					// 如果是材料题，要移动的试题sort不能大于本材料下的试题最大sort
					$judgeQuestionSort = max(ExamQuestion::where('partStem',$question->partStem)->pluck('sort')->toArray());
					$stem = ExamPartStem::find($question->partStem);
					if($question->sort < $judgeQuestionSort){
						if(ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort+1]])->exists()){
							$judgeQuestion = ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort+1]])->first();
							$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort+1]);
							if(ExamQuestion::where('id',$judgeQuestion->id)->exists()){
								ExamQuestion::where('id',$judgeQuestion->id)->update(['sort'=>$question->sort]);
							}
			    			// 这里还要判断是不是后移了qid对应的question
			    			if($stem->qid == $question->id){
			    				ExamPartStem::where('id',$stem->id)->update(['qid'=>$judgeQuestion->id]);
			    			}
						}else{
							$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort+1]);
						}
					}else{
						$result = '0';
					}
				}else{
					// 试题不是材料题，且不在最后一位
					if(ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort+1]])->exists()){
						$judgeQuestion = ExamQuestion::where([['exam_id',$question->exam_id],['sort',$question->sort+1]])->first();
						if($judgeQuestion->partStem){
			    			$result = '0';
			    		}else{
			    			$result = ExamQuestion::where('id',$id)->update(['sort'=>$judgeQuestion->sort]);
			    			if(ExamQuestion::where('id',$judgeQuestion->id)->exists()){
								ExamQuestion::where('id',$judgeQuestion->id)->update(['sort'=>$question->sort]);
							}
			    		}
					}else{
						$result = ExamQuestion::where('id',$id)->update(['sort'=>$question->sort+1]);
					}
					
				}
				$result =  $result ? '1':'0';
		        if($result){
		            Event(new ExamQuestionMoveBackwardEvent($question));
		        }	
			}
    	}elseif ($move==3) {
    		// 该id是试题的id
	    	$question = ExamQuestion::find($id);
	    	if($question->partStem){
	    		// 如果是材料题，需要判断是不是刚好是qid
	    		$stem = ExamPartStem::find($question->partStem);
	    		if($stem->qid == $question->id){
	    			$judgeQuestions = ExamQuestion::where('partStem',$stem->id)->get();
	    			if(count($judgeQuestions) == '1'){
	    				ExamPartStem::where('id',$stem->id)->update(['qid'=>'0']);
	    			}else{
	    				$changeStemQuestion =  ExamQuestion::where([['partStem',$stem->id],['sort',$question->sort+1]])->first();
	    				ExamPartStem::where('id',$stem->id)->update(['qid'=>$changeStemQuestion->id]);
	    			}
	    		}
	    	}
	    	// 如果不是材料题，不需要判断，直接删除并改变剩余sort就好了
	    	$sortToChangeQuestions = ExamQuestion::where([['exam_id',$question->exam_id],['sort','>',$question->sort]])->get();
	    	foreach($sortToChangeQuestions as $change){
	    		ExamQuestion::where('id',$change->id)->update(['sort'=>$change->sort-1]);
	    	}
	        // 要删除选项
	        ExamQuestionOption::where('qid',$id)->delete();
	    	$result = ExamQuestion::where('id',$id)->delete();
	    	$result =  $result ? '1':'0';
	        if($result){
	            Event(new ExamQuestionDeletedEvent($question));
	        }
    	}
    	if($result)$qs = ExamQuestion::where('exam_id',$question->exam_id)->with('getQuestionOptions')->orderBy('sort','asc')->get();
        return ['success'=>$result ? true:false,'questions'=>$qs];
    }
    // 将试题移出材料，这个功能暂时不做，感觉没有必要
}
