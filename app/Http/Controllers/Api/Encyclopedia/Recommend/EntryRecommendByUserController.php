<?php

namespace App\Http\Controllers\Api\Encyclopedia\Recommend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Behavior;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EntryRecommendByUserController extends Controller
{
    //接收用户对词条的推荐，注意判断用户是否还有推荐的余量
    public function entryRecommendByUser(Request $request){
        $data = $request->data;
        $eid = $data['id'];
        $cid = $data['cid'];
        $user_id = auth('api')->user()->id;
        // $behavior_id = 1;
    	// 查看用户推荐余量，即24小时内的推荐量是否大于3
    	// $time = Carbon::now()->subDays(1);
    	$result = 0;
        // 推荐记录写入redis，再队列写入数据库
        // 1 用户的推荐次数
        $left = Redis::GET('entry:recommend:times:userid:'.$user_id);
        // 2 用户是否推荐过该词条,即是否用户推荐词条集合的成员
        $had = Redis::SISMEMBER('entry:recommend:userid:'.$user_id,$eid);
    	if($left < 3 && !$had){
    		// 写入推荐
    		$createtime = Carbon::now();
            // 根据实验，setex该字段会在一天后消失且null+1仍然=1
            Redis::SETEX('entry:recommend:times:userid:'.$user_id,86400,$left+1);
            // 永久存储推荐记录到用户推荐集合，返回添加到集合中的元素数量
            $result = Redis::SADD('entry:recommend:userid:'.$user_id,$eid);
            
    		$tem = Behavior::find(1)->score;
            // 推荐增加热度
            Redis::INCRBY('entry:temperature:'.$eid,$tem);
            Redis::ZINCRBY('entry:temperature:rank',$tem,$eid.':'.$cid);
            Redis::ZINCRBY('classification:temperature:rank',$tem,$cid);
            // 存入数据库推荐记录(由于找不到model执行队列，这里先同步执行数据库保存)
            EntryTemperatureRecord::recordAdd($eid,$user_id,1,Carbon::now());
    		// $result = 
    		$mes = '';
    	}elseif($left >= 3){
    		$mes = '推荐次数不足！';
    	}else{
    		$mes = '推荐失败，请确认是否存在推荐记录！';
    	}
    	return $data = [
    		'success'	=> $result? true : false,
    		'message'	=> $mes
    	];
    }
}
