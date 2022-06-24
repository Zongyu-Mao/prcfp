<?php

namespace App\Http\Controllers\Api\Publication\Article;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Keyword;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticlePicture;
use App\Home\Publication\Article\ArticleFocusUser;
use App\Home\Publication\Article\ArticleCollectUser;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Home\Cooperation\ArticleContributeValue;
use Illuminate\Support\Facades\Redis;

class ArticleCreateController extends Controller
{
    //创建著作
    public function create(Request $request){
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
		
		
		//创建
		if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = Article::articleCreate($request['cid'],$request['title'],$request['etitle'],$request['nature'],$user_id,$user_id,$user_id);
        } else {
        	Storage::disk('public')->delete(end($request['path']));
        	return $res = [
				'success'=> false
			];
        }
		// 上传的图片信息存入图片表
		$avatarPictureId = ArticlePicture::articlePictureAdd($result,'avatar','avatar',$path,$user_id,1,1);
		
		$timelimit = $request['dateline'];
		$deadline = Carbon::now()->addMonths($timelimit);
		//创建协作计划
        if($request['cooperation_title'] == NULL)
            $request['cooperation_title'] = '[协作计划]'.$request['title'];
        // result1返回的是整个cooperation
		$result1 = ArticleCooperation::articleCooperationCreate($result,$request['cid'],$request['cooperation_title'],$request['target_level'],$request['secret'],$timelimit,$deadline,$request['seeking'],$request['assign'],1,$user_id,$username);
        //更新词条表的封面图片id和协作计划id
        Article::where('id',$result)->update([
            'cover_id'=>$avatarPictureId,
            'cooperation_id'=>$result1->id
        ]);
        // 贡献写入
        $con = ArticleContributeValue::contributeAdd($result1->id,$user_id);
		//创建者自动收藏和关注词条
		$focusResult = ArticleFocusUser::articleFocus($user_id,$result);

		$collectResult = ArticleCollectUser::articleCollect($user_id,$result);
		// 初始化热度
        Redis::INCR('article:views:'.$result); //浏览
        Redis::ZINCRBY('article:temperature:rank',1,$result); //总热度榜
        Redis::ZINCRBY('classification:temperature:rank',1,$request['cid']);//分类总热度榜
        Redis::ZINCRBY('article:classification:temperature:rank:'.$request['cid'],1,$result);//内容带分类热度榜
        // 如果创建时作者没有关注该分类，应该将分类列入关注
		$res = [
			'success'=>$result?true:false,
			'contentId'=>$result,
			'cooId'=>$result1->id,
			'contentTitle'=>$request['title'],
			'focus'=>$focusResult,
			'collect'=>$collectResult,
			'con'=>$con
		];
		return $res;
    }
}
