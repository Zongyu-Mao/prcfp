<?php

namespace App\Http\Controllers\Api\Encyclopedia\Entry;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry\Reference\EntryReference;
use App\Home\Encyclopedia\Entry;
use App\Home\Classification;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EntryContentController extends Controller
{
    // 可能要添加一个函数由前端控制content的lock情况
    public function getEntryContentModifyKey(Request $request){
        // 如果没有$change,是查询，如有$change，为改动
        $id = $request->id;
        $change = $request->change;
        $result = false;
        $user_id = auth('api')->user()->id;
        // Redis::set('entryContentModifyKey:'.$id,0);
        $key = Redis::get('entryContentModifyKey:'.$id);
        // 设置过期时间是8小时
        if(!$key)$result = Redis::setex('entryContentModifyKey:'.$id,28800,$user_id);
        if($key==$user_id)$result = true;
        return ['success'=>$result];
    }

    // 释放key，在修改完成或修改页面被强行关闭的情况下
    public function releaseKey(Request $request) {
    	$id = $request->id;
        $user_id = auth('api')->user()->id;
    	$result = false;
    	if(Redis::get('entryContentModifyKey:'.$id)==$user_id){
    		// 如果确实被锁定了，释放
    		$result = Redis::set('entryContentModifyKey:'.$id,0);
    	}
    	return ['success'=>$result ? true:false];
    }
	// 该参数id是entryContent的id，其实更改基本与著作entry并没有什么关系了 
    public function entryContentModify(Request $request){
        // 这里没有考虑的是用户如果直接关闭窗口或者挂机，那这部分内容就没会一直锁定了
        $id = $request->id;
		$result = false;
		$msg = '操作过时。';
		$input = $request->content;
		// 完成编辑后，直接替换老章节，没有版本转换，仅有当前版本
		$content = $input['content'];
		$modifyReason = $input['reason'];
		$big = $input['big'];
		$ip = User::getClientIp();
		// dd($ip);
		$user_id = auth('api')->user()->id;
        $contents=[];
		// 更改内容表，新建本次编辑的内容
        if(Redis::get('entryContentModifyKey:'.$id)==$user_id){
            $result = EntryContent::entryContentModify($id,$content,$user_id,$ip,$big,$modifyReason);
            Redis::set('entryContentModifyKey:'.$id,0);
            if($result)$msg = '成功上传。';
        } else {
            $msg.='非法用户！';
        }
        if($result)$contents=EntryContent::where('eid',EntryContent::find($id)->eid)->orderBy('sort','asc')->get();
		return [
            'success'=>$result ? true:false,
            'contents'=>$contents,
            'msg'=>$msg
        ];

    }
}
