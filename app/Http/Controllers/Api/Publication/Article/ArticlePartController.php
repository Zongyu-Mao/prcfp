<?php

namespace App\Http\Controllers\Api\Publication\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Publication\Article\ArticleContent;
use App\Models\Publication\Article\ArticlePart;
use App\Home\Publication\Article\Reference\ArticleReference;
use App\Home\Publication\Article;
use App\Home\Classification;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticlePartController extends Controller
{
    // part加入后，连接article和content
    public function getArticlePartModifyKey(Request $request){
        // 如果没有$change,是查询，如有$change，为改动
        $data = $request->data;
        $id = $data['id'];
        $result = false;
        $user_id = Auth::user()->id;
        // Redis::set('articlePartModifyKey:'.$id,0);
        $key = Redis::get('articlePartModifyKey:'.$id);
        if(!$key)$result = Redis::set('articlePartModifyKey:'.$id,$user_id);
        if($key==$user_id)$result = true;
        return ['success'=>$result];
    }

    // 释放key，在修改完成或修改页面被强行关闭的情况下
    public function releaseKey(Request $request) {
        $user_id = Auth::user()->id;
    	$data = $request->data;
        $id = $data['id'];
    	$result = false;
    	if(Redis::get('articlePartModifyKey:'.$id)==$user_id){
    		// 如果确实被锁定了，释放
    		$result = Redis::set('articlePartModifyKey:'.$id,0);
    	}
    	return ['success'=>$result ? true:false];
    }
	// 该参数id是articlePart的id，其实更改基本与著作Article并没有什么关系了 
    public function articlePartModify(Request $request){
        // 这里没有考虑的是用户如果直接关闭窗口或者挂机，那这部分内容就没会一直锁定了
        $data = $request->data;
        $id = $data['part_id'];
		$result = false;
        $title = $data['title'];
		$user_id = Auth::user()->id;
        // 解除锁定
        $lock = 0;
		// 更改内容表，新建本次编辑的内容
        if(Redis::get('articlePartModifyKey:'.$id)==$user_id){
            $result = ArticlePart::partModify($id,$title);
            Redis::set('articlePartModifyKey:'.$id,0);
            if($result)$msg = '成功上传。';
        } else {
            $msg.='非法用户！';
        }
        if($result)$parts=ArticlePart::where('aid',ArticlePart::find($id)->aid)->orderBy('sort','asc')->get();
        $part = $parts->where('id',$id)->first();
        
		return [
            'success'=>$result ? true:false,
            'parts'=>$parts,
            'part'=>$part,
            'msg'=>$msg
        ];

    }
}
