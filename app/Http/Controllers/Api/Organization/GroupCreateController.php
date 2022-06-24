<?php

namespace App\Http\Controllers\Api\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Classification;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupEmblem;
use App\Home\Organization\Group\GroupFocusUser;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class GroupCreateController extends Controller
{
    //创建组织
    public function groupCreate(Request $request){
    	// return $request->data;
    	$user = auth('api')->user();
		$data = $request->data;
		$user_id = $user->id;
        $username= $user->username;
		// 得到词条的封面图片
		if(count($data['path'])>1){
			$pathD = $request['path'];
			for($i=0;$i<count($pathD)-1;$i++){
				Storage::disk('public')->delete($pathD[$i]);
			}
		}
		$path = '/storage/' . end($data['path']);
		
		//创建组织
		$result = Group::groupCreate($data['cid'],$data['title'],$data['introduction'],$data['seeking'],$username,$user_id,$username,$user_id);

		// 上传的徽章信息存入徽章表
		$emblemId = GroupEmblem::emblemAdd($result,$path);
		GroupFocusUser::groupFocus($user_id,$result);
		// 初始化热度
        Redis::INCR('group:views:'.$result); //浏览
        Redis::ZINCRBY('group:temperature:rank',1,$result); //百科总热度榜
        Redis::ZINCRBY('classification:temperature:rank',1,$data['cid']);//分类总热度榜
        Redis::ZINCRBY('group:classification:temperature:rank:'.$data['cid'],1,$result);//内容带分类热度榜

        //更新组织徽章
        Group::where('id',$result)->update([
            'emblem'=>$emblemId
        ]);

        // 如果创建时作者没有关注该分类，不能创建，这个可以在选择时判断
		$return = [
			'id'=>$result,
			'success'=>$result ? true: false,
			'title'=>$data['title'],
		];
		return $return;
	}
}
