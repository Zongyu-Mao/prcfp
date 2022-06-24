<?php

namespace App\Http\Controllers\Api\Examination\PartStem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPartStem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PartStemCreateController extends Controller
{
    public function partStemCreate(Request $request){
    	$exam_id = $request->exam_id;
    	$id = $request->stem_id;
    	$position = $request->move;
    	$partStem = $request->get('partStem');
    	$user = auth('api')->user();
    	$ip = User::getClientIp();
    	$qid = '0';
    	$questions = '0';
    	$lock = 0;
    	$big = 1;
        $stems = '';
    	if($id==0 && $position<0){
    		// 全新创建 没有参考
    		$reason = 'new partStem';
    		$sort = ExamPartStem::where('exam_id',$exam_id)->exists()?max(ExamPartStem::where('exam_id',$exam_id)->pluck('sort')->toArray())+1 : 1;
    		$result = ExamPartStem::examPartStemCreate($exam_id,$sort,$qid,$questions,$partStem,$lock,$user->id,$user->id,$ip,$big,$reason);
    	}
    	if($id){
    		$stem = ExamPartStem::find($id);
    		if($partStem && $position == '0'){
	     		$exam_id = Exam::find($stem->exam_id)->id;
	     		// 新建材料没有问题，因此qid=0
	     		$qid = '0';
    			$questions = '0';
    			$lock = 0;
    			$big = 1;
    			$reason = '在原材料（'.$stem->sort.'）之前创建新的材料。';
    			$changes = ExamPartStem::where([['exam_id',$exam_id],['sort','>=',$stem->sort]])->get();
    			foreach($changes as $change){
    				ExamPartStem::where('id',$change->id)->update(['sort'=>$change->sort+1]);
    			}
    			$result = ExamPartStem::examPartStemCreate($exam_id,$stem->sort,$qid,$questions,$partStem,$lock,$user->id,$user->id,$ip,$big,$reason);
    			
    		}elseif($partStem && $position == '1'){
    			$exam_id = Exam::find($stem->exam_id)->id;
                $qid = '0';
    			$questions = '0';
    			$lock = 0;
    			$big = 1;
    			$reason = '在原材料（'.$stem->sort.'）之后创建新的材料。';
    			$changes = ExamPartStem::where([['exam_id',$exam_id],['sort','>',$stem->sort]])->get();
    			foreach($changes as $change){
    				ExamPartStem::where('id',$change->id)->update(['sort'=>$change->sort+'1']);
    			}
    			$result = ExamPartStem::examPartStemCreate($exam_id,$stem->sort+'1',$qid,$questions,$partStem,$lock,$user->id,$user->id,$ip,$big,$reason);
    			
    		}
    	}
    	if($result)$stems = ExamPartStem::where('exam_id',$exam_id)->orderBy('sort','asc')->get();
        return ['success'=>$result?true:false,'stems'=>$stems];
    }
}
