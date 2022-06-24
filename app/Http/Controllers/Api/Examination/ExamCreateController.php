<?php

namespace App\Http\Controllers\Api\Examination;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;use App\Home\Classification;
use App\Home\Keyword;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPicture;
use App\Home\Examination\Exam\ExamCollectUser;
use App\Home\Examination\Exam\ExamFocusUser;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Home\Cooperation\ExamContributeValue;
use Illuminate\Support\Facades\Redis;

class ExamCreateController extends Controller
{
    public function create(Request $request) {
    	//接收表单数据
		$request = $request->all();
		$user = auth('api')->user();
		$user_id = $user->id;
		$username = $user->username;
        // $deleteFileName = $request->file('file');
        // Storage::disk('public')->delete($deleteFileName);
		// 得到封面图片
		if(count($request['path'])>1){
			$pathD = $request['path'];
			for($i=0;$i<count($pathD)-1;$i++){
				Storage::disk('public')->delete($pathD[$i]);
			}
		}
		$path = end($request['path']);
		$summary = '';
		$request['nature']=1;
		//创建
		if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = Exam::examCreate($request['cid'],$request['title'],$request['etitle'],$summary,$request['nature'],$user_id,$user_id,$user_id);
        }
        
		// 上传的图片信息存入图片表
		$avatarPictureId = ExamPicture::examPictureAdd($result,'avatar','avatar',$path,$user_id,1,1);
		
		$timelimit = $request['dateline'];
		$deadline = Carbon::now()->addMonths($timelimit);
		//创建协作计划
        if($request['cooperation_title'] == NULL)
            $request['cooperation_title'] = '[协作计划]'.$request['title'];
		$result1 = ExamCooperation::examCooperationCreate($result,$request['cid'],$request['cooperation_title'],$request['target_level'],$timelimit,$deadline,$request['seeking'],$request['assign'],1,$user_id,$username);
        //更新词条表的封面图片id和协作计划id
        Exam::where('id',$result)->update([
            'cover_id'=>$avatarPictureId,
            'cooperation_id'=>$result1
        ]);
        // 贡献写入
        $con = ExamContributeValue::contributeAdd($result1,$user_id);
		//创建者自动收藏和关注词条
		$focusResult = ExamFocusUser::examFocus($user_id,$result);
		$collectResult = ExamCollectUser::examCollect($user_id,$result);
		// 初始化热度
        Redis::INCR('exam:views:'.$result); //浏览
        Redis::ZINCRBY('exam:temperature:rank',1,$result); //总热度榜
        Redis::ZINCRBY('classification:temperature:rank',1,$request['cid']);//分类总热度榜
        Redis::ZINCRBY('exam:classification:temperature:rank:'.$request['cid'],1,$result);//内容带分类热度榜
        // 如果创建时作者没有关注该分类，应该将分类列入关注
		$return = [
			'success'=>$result?true:false,
			'contentId'=>$result,
			'cooId'=>$result1,
			'contentTitle'=>$request['title'],
			'focus'=>$focusResult,
			'collect'=>$collectResult,
			'con'=>$con
		];
		return $return;
    }
}
