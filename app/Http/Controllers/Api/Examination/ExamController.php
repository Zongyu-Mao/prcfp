<?php

namespace App\Http\Controllers\Api\Examination;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam\ExamQuestion;
use App\Home\Examination\Exam\ExamPartStem;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\Recommend\ExamTemperatureRecord;
use App\Home\Examination\Recommend\ExamTemperature;
use App\Home\Personnel\Behavior;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Home\Classification;
use Illuminate\Support\Facades\Auth;
use App\Models\Committee\Surveillance\SurveillanceExamRecord;
use App\Models\Committee\Surveillance\SurveillanceExamMark;
use App\Models\Committee\Surveillance\SurveillanceExamWarning;


class ExamController extends Controller
{
    // 显示exam详情页
    public function examDetail(Request $request,$id,$examTitle){
    	$data = Exam::find($id);
        $return = [];
     	if($id && $data->title==$examTitle){
     		// 更改关联结构，由题目归属材料，如果没有材料直接显示题目，question里的part更改为stem的id，但是如果这样的话，stem和非stem的question不知道怎么排序
     		// 如果按以前的思路，将材料与第一个qid绑定，在question表里，part有stem的id或者0区别是否属于材料题
     		// 材料自带问题，其他的问题是非材料问题examQuestions
     		$examQuestions = ExamQuestion::where('exam_id',$id)->with('getQuestionOptions')->orderBy('sort','asc')->get();
     		// dd($examQuestions);
     		$partStems = ExamPartStem::where('exam_id',$id)->orderBy('sort','asc')->get();
            // $data->getExamContents()->get();
            // 这里目前不考虑用Exam模型的一对多关联，直接在ExamContent模型里取content内容
            // dd($ExamContents);
            Redis::INCR('exam:views:'.$data->id);
            Redis::INCR('exam:temperature:'.$data->id);
            // 更新排行榜热度
            Redis::ZINCRBY('exam:temperature:rank',1,$data->id);
            // 分类榜
            Redis::ZINCRBY('exam:classification:temperature:rank:'.$data->cid,1,$data->id);
            Redis::ZINCRBY('classification:temperature:rank',1,$data->cid);
            // 此处热度是在Redis下，没有在Cache下
     		$temperature = Redis::GET('exam:temperature:'.$data->id);
            $surveillances = SurveillanceExamRecord::where('sid',$id)->exists()?SurveillanceExamRecord::where('sid',$id)->get():'';
            $marks = SurveillanceExamMark::where('sid',$id)->exists()?SurveillanceExamMark::where('sid',$id)->get():'';
            $warnings = SurveillanceExamWarning::where('sid',$id)->exists()?SurveillanceExamWarning::where('sid',$id)->get():'';
            $cooperation = ExamCooperation::find($data->cooperation_id);
            $crewArr = $cooperation?$cooperation->crews()->pluck('user_id')->toArray():[];
            if($data->manage_id)array_push($crewArr,$data->manage_id);
 			$cid = $data->cid;
            $user = auth('api')->user();
 			$user_id = $user->id;
            $role = $user->getRole;
            $committee = $user->getCommittee;
     		$data_class = Classification::getClassPath($cid);
            $extendedExams = $data->extendedExamReading()->get();
            $extendedArticles = $data->extendedArticleReading()->get();
            $extendedEntries = $data->extendedEntryReading()->get();
     		$focus = $data->examFocus()->find($user_id);
     		$collect = $data->examCollect()->find($user_id);
     		// dd($keywords);
            $behavior_id = 1;
            $rec_check = ExamTemperatureRecord::where([['exam_id',$id],['behavior_id',$behavior_id],['user_id',$user_id]])->count();
            $keywords = $data->keywords()->get();
            $reference = [];
     		$examExtraData = [
     			'focus' => $focus,
     			'collect'=>$collect
     		];
     		$return = [
     			'exam' => $data,
                'class' => $data_class,
                'crewArr' => $crewArr,
     			'examQuestions' => $examQuestions,
     			'partStems' => $partStems,
     			'ex_exams' => $extendedExams,
     			'ex_entries' => $extendedEntries,
     			'ex_articles' => $extendedArticles,
                'focus' => $focus,
     			'collect' => $collect,
     			'keywords' => $keywords,
     			'rec_check' => $rec_check,
     			'reference' => $reference,
                'surveillances' => $surveillances,
                'marks' => $marks,
                'warnings' => $warnings,
                'tem' => $temperature,
                'user' => $user,
     		];
     	}
        return $return;
    }
}
