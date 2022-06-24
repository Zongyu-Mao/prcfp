<?php

namespace App\Http\Controllers\Api\Personnel\Medal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Medal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Storage;

class MedalModifyController extends Controller
{
    // 创建新的功章
    public function medalCreate(Request $request){
        $isNew = $request->isNew;
        $medal_id = $request->medal_id;
        $suit_id = $request->suit_id;
    	$user_id = $request->user_id;
    	$suit = MedalSuit::find($suit_id);
    	$amount = $request->amount;
    	$maxSort = $suit->amount;
    	$sort = $request->sort;
    	$sort = $sort<$maxSort ? $sort:$maxSort;
    	$suit_title = $request->suit_title;		
        $description = $request->description;
        $title = $request->title;
		$weight = $request->weight;
        $re_path = $request->path;
        $result = false;
        $medalSuit = '';
        // return $re_path;
		$creator_id = auth('api')->user()->id;
        if(count($re_path)>1){
            $pathD = $re_path;
            for($i=0;$i<count($pathD)-1;$i++){
                Storage::disk('public')->delete($pathD[$i]);
            }
        }
        $path = '/storage/' . end($re_path);
        // return ['paht'=>$path];
        if($isNew){
            // 处理排序:如果medal个数小于总个数，可以写入
            if(count($suit->getMedals) < $maxSort){
                $sort = $sort>0 ? $sort:1;
                if(Medal::where([['suit_id',$suit_id],['sort',$sort]])->exists()){
                    $oldMedals = Medal::where([['suit_id',$suit_id],['sort','>=',$sort]])->get();
                    foreach($oldMedals as $m){
                        $s = $m->sort+1;
                        $s = $s<$maxSort ? $s:$maxSort;
                        Medal::where('id',$m->id)->update(['sort'=>$s]);
                    }
                }
                // 写入
                $result = Medal::medalAdd($suit_id,$sort,$weight,$path,$title,$description,$creator_id);
            }
        } else {
            if(Redis::get('medalModifyKey:'.$medal_id)==$user_id){
                $medal = Medal::find($medal_id);
                if($sort!=$medal->sort && Medal::where([['suit_id',$medal->suit_id],['sort',$sort]])->exists()){
                    Medal::where([['suit_id',$medal->suit_id],['sort',$sort]])->update(['sort'=>$medal->sort]);
                }
                $result = Medal::medalModify($medal_id,$sort,$weight,$path,$title,$description);
                Redis::set('medalModifyKey:'.$medal_id,0);
            } 
        }
		if($result) {
            $medalSuit = MedalSuit::where('id',$suit_id)->with('getMedals')->first();
        }
    	return [
    		'success'	=>  $result? true:false,
            'medalSuit' =>  $medalSuit
    	];
    }
    //删除id的功章
    public function medalDelete(Request $request){
        $medal_id = $request->input('medal_id');
    	$user_id = $request->input('user_id');
        $result = false;
        if($user_id == auth('api')->user()->id && !Redis::get('medalModifyKey:'.$medal_id)){
           $result = Medal::medalDelete($medal_id); 
        }
    	return [
			'success'	=> $result? true:false
    	];
    }

    // 可能要添加一个函数由前端控制content的lock情况
    public function getMedalModifyKey(Request $request){
        // 如果没有$change,是查询，如有$change，为改动
        $medal_id = $request->medal_id;
        $userId = $request->user_id;
        $result = false;
        $user_id = auth('api')->user()->id;
        // Redis::set('medalModifyKey:'.$id,0);
        $key = Redis::get('medalModifyKey:'.$medal_id);
        if($userId==$user_id){
            // 设置过期时间是1小时
            if(!$key)$result = Redis::setex('medalModifyKey:'.$medal_id,3600,$user_id);
            if($key==$user_id)$result = true;
        }
        return ['success'=>$result];
    }

    // 释放key，在修改完成或修改页面被强行关闭的情况下
    public function releaseKey(Request $request) {
        $id = $request->medal_id;
        $user_id = auth('api')->user()->id;
        $result = false;
        if(Redis::get('medalModifyKey:'.$id)==$user_id){
            // 如果确实被锁定了，释放
            $result = Redis::set('medalModifyKey:'.$id,0);
        }
        return ['success'=>$result ? true:false];
    }
}
