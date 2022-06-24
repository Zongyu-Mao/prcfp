<?php

namespace App\Http\Controllers\Api\Examination\Question;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPartStem;
use App\Home\Examination\Exam\ExamQuestion;
use App\Home\Examination\Exam\Question\ExamQuestionOption;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuestionCreateController extends Controller
{
    //
    public function questionCreate(Request $request) {
    	// return $request;
    	// 完全合并试题新建操作，exam_id是必须的，如果有stem_id，且type==1，就一定是材料题，如果有stem_id且type==2，就是材料下的题，（没有position）如果等于qid的sort就放在材料前，如果大于qid的sort就放在材料后，有position就按position来，还要考虑question_id
    	// 如果非材料题，就插在材料最大sort题之后或者最小sort题之前
    	$question_id = $request->question_id;
    	$exam_id = $request->exam_id;
    	$stem_id = $request->stem_id;
    	$data = $request->question;
    	$questionStem = $data['stem'];
    	$position = $request->position;
    	$type = $request->type;
    	$lock = '0';
    	$user = auth('api')->user();
    	$ip = User::getClientIp();
    	$big = 1;
    	$reason = 'new question';
    	$annotation = $data['annotation'];
    	$answer = $data['answer'];
    	$score = $data['score'];
    	// 目前option个数直接定为4；
    	$options = 4;
        $qs = '';
    	$option = [
    		'1' => $data['optionA'],
    		'2' => $data['optionB'],
    		'3' => $data['optionC'],
    		'4' => $data['optionD']
    	];
    	// answer已经在前端处理过了，这里不需要处理
    	// 最后再处理总的sort的问题
    	if(!$stem_id && !$question_id){
    		// 如果没有材料和问题的参照，默认sort=1
            
    		$partStem = 0;
    		$sort = 1;
    		// 先转换sort
    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
			foreach($questions as $question){
				ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
			}
	        $questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
    	}elseif ($stem_id && !$question_id) {
    		// 有材料但是无问题，默认放入stem的第一，如果有qid直接替换，如果没有qid，直接写入
    		// 由于没有question_id，position必然为空，这时候需要得到本材料及之前材料的最大question的sort
    		$examPartStem = ExamPartStem::find($stem_id);
    		$sort = 1;
    		$partStem = $stem_id;
    		if($examPartStem->sort>1){
    			$stemArr = ExamPartStem::where([['exam_id',$exam_id],['sort','<=',$examPartStem->sort]])->pluck('id')->toArray();
    			$sort = max(ExamQuestion::whereIn('id',$stemArr)->pluck('sort')->toArray());
    			$sort = ($sort ? $sort+1 : 1);
    		}
    		// 但是要防止用户将type_stem 改掉了
    		if($data['type_stem']==2){
    			$partStem = 0;
    		}
    		// 先转换sort
    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
			foreach($questions as $question){
				ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
			}
			// 再写入问题
	        $questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
	        if($data['type_stem']==1){
	        	ExamPartStem::where('id',$stem_id)->update(['qid'=>$questionId]);
	        }
	        
    	}elseif ($stem_id && $question_id) {
    		// 如果有材料且有问题，根据type_c判断类型，根据position插入问题
    		$examPartStem = ExamPartStem::find($stem_id);
    		$partStem = $stem_id;
    		$q_sort = ExamQuestion::find($question_id)->sort;
    		if($position==1 || $position==0){
    			// 在问题前写入问题，需要判断被比较问题是否在最前
    			if($data['type_stem']==1){
    				// 是材料题
    				$sort = $q_sort;
    				// 先转换sort
		    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
					foreach($questions as $question){
						ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
					}
					// 再写入问题
    				$questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
    				if($examPartStem->qid == $question_id){
	    				// 如果是材料的最先问题，需要改变qid
	    				ExamPartStem::where('id',$stem_id)->update(['qid'=>$questionId]);
	    			}
    			}elseif($data['type_stem']==2){
    				// 不是材料题，此时要将sort排到所有材料题之前，即qid之前，但是不用再管材料内部题
    				$sort = ExamQuestion::find($examPartStem->qid)->sort;;
    				// if(ExamQuestion::where([['exam_id',$exam_id],['sort','<',$q_sort],['partStem',0]])->exists()){
    				// 	$sort = max(ExamQuestion::where([['exam_id',$exam_id],['sort','<',$q_sort],['partStem',0]])->pluck('sort')->toArray());
    				// }else{
    				// 	$sort = 1;
    				// }
    				$partStem = 0;
    				// 先转换sort
		    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
					foreach($questions as $question){
						ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
					}
    				$questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
    			}
    			
    		}elseif($position==2){
    			if($data['type_stem']==1){
    				// 是材料题
    				$sort = $q_sort+1;
    			}elseif($data['type_stem']==2){
    				// 不是材料题，此时要将sort排到所有材料题之前，但是不用再管材料内部题，但是由于退出stem，可能sort不存在
    				if(ExamQuestion::where('partStem',$stem_id)->exists()){
    					$sort = max(ExamQuestion::where('partStem',$stem_id)->pluck('sort')->toArray())+1;
    				}else{
    					$sort = 1;
    				}
    				$partStem = 0;
    				
    			}
    			// 先转换sort
	    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
				foreach($questions as $question){
					ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
				}
    			$questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
    		}
    	}elseif (!$stem_id && $question_id) {
    		// 如果没有材料且有问题，直接根据position插入问题
    		$partStem = 0;
    		$q_sort = ExamQuestion::find($question_id)->sort;
    		if($position==1){
				$sort = $q_sort;
    		}elseif($position==2){
				$sort = $q_sort+1;
    		}
    		// 先转换sort
    		$questions = ExamQuestion::where([['exam_id',$exam_id],['sort','>=',$sort]])->get();
			foreach($questions as $question){
				ExamQuestion::where('id',$question->id)->update(['sort'=>$question->sort+1]);
			}
    		$questionId = ExamQuestion::examQuestionCreate($exam_id,$data['score'],$type,$partStem,$questionStem,$options,$sort,$answer,$annotation,$lock,$user->id,$user->id,$ip,$big,$reason);
    	}

    	// 最后如果写入问题成功后写入选项
    	if($questionId){
    		foreach($option as $key=>$value){
	            ExamQuestionOption::questionOptionCreate($questionId,$value,$key,$user->id,$user->id,$ip,$big,$reason);
	        }
            $qs = ExamQuestion::where('exam_id',$exam_id)->with('getQuestionOptions')->orderBy('sort','asc')->get();
    	}
        return ['success'=>$questionId ? true:false,'questions'=>$qs];
    }
    
}
