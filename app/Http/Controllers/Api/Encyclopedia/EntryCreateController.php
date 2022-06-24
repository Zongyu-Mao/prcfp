<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Keyword;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryPicture;
use App\Home\Encyclopedia\Entry\EntryCollectUser;
use App\Home\Encyclopedia\Entry\EntryFocusUser;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Home\Cooperation\EntryContributeValue;
use Illuminate\Support\Facades\Redis;
use App\Models\Encyclopedia\Ambiguity\Polysemant;

class EntryCreateController extends Controller
{
    //
    public function create(Request $request) {
    	//接收表单数据
		$request = $request->all();
		$user = auth('api')->user();
		$user_id = $user->id;
		$username = $user->username;
		$createtime = Carbon::now();
        // $deleteFileName = $request->file('file');
        // Storage::disk('public')->delete($deleteFileName);
		// 得到封面图片
		if(count($request['path'])>1){
			$pathD = $request['path'];
			for($i=0;$i<count($pathD)-1;$i++){
				Storage::disk('public')->delete($pathD[$i]);
			}
		}
		// 取消storage前缀
		$path = end($request['path']);

		//创建词条
		// 这里要先扣去用户金币
        if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
        	$status=0;//不再需要歧义项的3
            $result = Entry::entryCreate($request['cid'],$request['title'],$request['etitle'],$user_id,$user_id,$user_id,$status);
        }
		// 上传的图片信息存入图片表
		$avatarPictureId = EntryPicture::entryPictureAdd($result,'avatar','avatar',$path,$user_id,1,1);
		
		$timelimit = $request['dateline'];
		$deadline = $createtime->addMonths($timelimit);
		//创建协作计划
        if($request['cooperation_title'] == NULL)$request['cooperation_title'] = '[协作计划]'.$request['title'];
		$result1 = EntryCooperation::entryCooperationCreate($result,$request['cid'],$request['cooperation_title'],$request['target_level'],$timelimit,$deadline,$request['seeking'],$request['assign'],1,$user_id,$username);
        //更新词条表的封面图片id和协作计划id
        Entry::where('id',$result)->update([
            'cover_id'=>$avatarPictureId,
            'cooperation_id'=>$result1->id
        ]);
        // 初始化热度
        Redis::INCR('entry:views:'.$result); //浏览
        Redis::ZINCRBY('entry:temperature:rank',1,$result); //百科总热度榜
        Redis::ZINCRBY('classification:temperature:rank',1,$request['cid']);//分类总热度榜
        Redis::ZINCRBY('entry:classification:temperature:rank:'.$request['cid'],1,$result);//内容带分类热度榜
        if($request['polysemant']) {
        	// 建立强制的歧义关系,由于经过创建筛选，因此此处默认p内容为正常内容
        	// $sta = Entry::find($request['poly_id'])->status;
        	// if($sta<2)Entry::where('id',$request['polysemant'])->update(['status'=>3]);
         //    if($sta==2)Entry::where('id',$request['polysemant'])->update(['status'=>4]);
        	$pids = Entry::where('title',$request['title'])->pluck('id')->toArray();
        	foreach($pids as $p) {
        		Polysemant::newPolysemant($result,$p,$user->id,$createtime);
        	}
        	
        }
        // 贡献写入
        $con = EntryContributeValue::contributeAdd($result1->id,$user_id);
		//创建者自动收藏和关注词条
		$focusResult = EntryFocusUser::entryFocus($user_id,$result);

		$collectResult = EntryCollectUser::entryCollect($user_id,$result);
        // 如果创建时作者没有关注该分类，应该将分类列入关注
		$request = [
			'success'=>$result?true:false,
			'contentId'=>$result,
			'cooId'=>$result1->id,
			'contentTitle'=>$request['title'],
			'focus'=>$focusResult,
			'collect'=>$collectResult,
			'con'=>$con
		];
		return $request;
    }
}
