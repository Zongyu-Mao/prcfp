<?php

namespace App\Http\Controllers\Api\Examination\PartStem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPartStem;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PartStemModifyController extends Controller
{
	// 可能要添加一个函数由前端控制content的lock情况
    public function getExamStemModifyKey(Request $request){
        // 如果没有$change,是查询，如有$change，为改动
        $id = $request->id;
        $change = $request->change;
        $user_id = auth('api')->user()->id;
        $result = false;
        $key = Redis::get('examPartstemModifyKey:'.$id);
        // $key = Redis::del('examPartstemModifyKey:'.$id);
        if(!$key)$result = Redis::set('examPartstemModifyKey:'.$id,$user_id,'EX',7200);
        if($key==$user_id)$result = true;
        return ['success'=>$result?true:false,'key'=>Redis::get('examPartstemModifyKey:'.$id),'user'=>$user_id,'s'=>$id];
    }

    // 释放key，在修改完成或修改页面被强行关闭的情况下
    public function releaseKey(Request $request) {
    	$id = $request->id;
    	$result = false;
    	$user_id = auth('api')->user()->id;
    	if(Redis::get('examPartstemModifyKey:'.$id)==$user_id){
    		// 如果确实被锁定了，释放
    		$result = Redis::set('examPartstemModifyKey:'.$id,0);
    	}
    	return ['success'=>$result ? true:false];
    }

    // 处理编辑的数据
    public function partStemModify(Request $request){
    	$stem = $request->get('partStem');
    	$id = $request->id;
    	$result = false;
		$lock = 0;
    	$user = auth('api')->user()->id;
    	$ip = User::getClientIp();
    	$big = 0;
        $stems = '';
    	$reason = 'part_stem modify';
		$result = ExamPartStem::examPartStemModify($id,$stem,$lock,$user,$ip,$big,$reason);
        if($result)$stems = ExamPartStem::where('exam_id',ExamPartStem::find($id)->exam_id)->orderBy('sort','asc')->get();
		return ['success'=>$result ? true:false,'stems'=>$stems];
    }
}
