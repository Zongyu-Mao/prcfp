<?php

namespace App\Http\Controllers\Api\Publication\ArticleReview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ArticleReviewCreateController extends Controller
{
    //评审计划的创建
    public function create(Request $request){
    	$id = $request->id;
    	$input = $request->input;
    	$result = false;
    	$article = Article::where('id',$id)->first();
        $level = $article->level;
    	$kpi_count = ArticleReview::where([['aid',$id],['status','0']])->exists() ? '1':'0';
    	//判断是否存在词条的反对意见
    	$article_oppose_count = ArticleOpponent::where([['aid',$id],['status','0']])->exists() ? '1':'0';
    	if($request->isMethod('post') && $kpi_count == '0'){
            //接收留言内容并写入数据表 
            $target = $input['target'];
            $cid = $input['cid'];
            $timelimit = $input['timelimit'];
            $deadline = Carbon::now()->addDays($timelimit*15);
            $title = $input['title'];
            $content = $input['content'];
            $initiate_id =auth('api')->user()->id;
            $initiate = auth('api')->user()->username;
            $articleTitle = $article->title;
            // return $data1;
            if($title && $content && $target == $level+1){
                // 创建评审计划
                $result = ArticleReview::reviewCreate($id,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$articleTitle);
                //发表了有效的讨论后，积分和成长值+5
                $result1 = User::expAndGrowValue($initiate_id,100,100);
                Article::where('id',$id)->update(['review_id' => $result]);
            }
        }
        return ['success'=>$result? true:false];	
    }
}
