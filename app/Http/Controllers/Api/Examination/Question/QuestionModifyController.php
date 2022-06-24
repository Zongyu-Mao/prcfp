<?php

namespace App\Http\Controllers\Api\Examination\Question;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamQuestion;
use App\Home\Examination\Exam\Question\ExamQuestionOption;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class QuestionModifyController extends Controller
{
	// 得到编辑锁
	public function getQuestionModifyKey(Request $request){
        // 如果没有$change,是查询，如有$change，为改动
        $id = $request->id;
        $change = $request->change;
        $user_id = auth('api')->user()->id;
        $key = Redis::get('examQuestionModifyKey:'.$id);
        if(!$key)$result = Redis::set('examQuestionModifyKey:'.$id,$user_id,'EX',7200);
        if($key==$user_id)$result = true;
        return ['success'=>$result];
    }

    // 释放key，在修改完成或修改页面被强行关闭的情况下
    public function releaseQuestionModifyKey(Request $request) {
    	$id = $request->id;
    	$result = false;
    	$user_id = auth('api')->user()->id;
    	if(Redis::get('examQuestionModifyKey:'.$id)==$user_id){
    		// 如果确实被锁定了，释放
    		$result = Redis::set('examQuestionModifyKey:'.$id,0);
    	}
    	return ['success'=>$result ? true:false];
    }

    // 修改除选项和答案、分数外的内容
    public function questionModify(Request $request){
        $content = $request->content;
        $id = $request->id;
        $exam_id = $request->exam_id;
        $type = $request->type;
        $type_q = $request->type_q;
        $result = false;
        $user = auth('api')->user();
        $ip = User::getClientIp();
        $lock = 0;
        $big = 0;
        if($type==1){
            $reason = 'question stem modify';
            $result = ExamQuestion::questionStemModify($id,$content,$lock,$user->id,$ip,$big,$reason);
        }elseif($type==2){
            $reason = 'option modify';
            $result = ExamQuestionOption::questionOptionModify($id,$content,$user->id,$ip,$big,$reason);
        }elseif($type==3){
            $result = ExamQuestion::questionAnswerModify($id,$content,$lock,$user->id,$ip);
        }elseif($type==4){
            $reason = 'annotation modify';
            $result = ExamQuestion::questionAnnotationModify($id,$content,$lock,$user->id,$ip,$big,$reason);
        }elseif($type==5){
            $reason = 'score modify';
            $s = ExamQuestion::find($id)->score;
            $total = Exam::find($exam_id)->total;
            $result = ExamQuestion::questionScoreModify($id,$content,$lock,$user->id,$ip,$big,$reason);
            Exam::totalUpdate($exam_id,$content-$s+$total);
        }
        if($result)$qs = ExamQuestion::where('exam_id',$exam_id)->with('getQuestionOptions')->orderBy('sort','asc')->get();
        return ['success'=>$result ? true:false,'questions'=>$qs];
    }

}
