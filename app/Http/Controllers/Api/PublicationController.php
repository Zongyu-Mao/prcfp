<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\Recommend\ArticleTemperature;
use App\Home\Publication\Recommend\ArticleRecommendation;
use App\Home\Publication\articleDiscussion\ArticleOpponent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PublicationController extends Controller
{
    //著作首页
    public function getPublicationRecommends(){
    	$user = auth('api')->user();
        $id = $user->id;
        $interest=$user->getInterest->pluck('id')->toArray();
        $nosc = false;
        // 主专业
        $sc_id = $user->specialty;
        if($sc_id==0 && count($interest)>0){
            $sc_id=$interest[0];
        } else {
            $nosc = !$nosc;
            $sc_id = 38;//注意这里专业排行还没有 classificationTRank
        }
        if($sc_id) {
            // 本分类下的内容排行取值
            $a = Article::where('id',(Redis::ZREVRANGE('article:classification:temperature:rank:'.$sc_id,0,1)[0]))->with('articleAvatar')->with('classification')->first();
            $as = Article::whereIn('id',Redis::ZREVRANGE('article:classification:temperature:rank:'.$sc_id,4,6))->get(['id','title']);
            $resorts = ArticleResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(10)->get();
            // 百科首页的协作评选求助讨论辩论(协作因为与主内容绑定，因此不在此列)
            $reviews = ArticleReview::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();

            $debates = ArticleDebate::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();
        }
        $announcements = Announcement::where('scope','2')->orderBy('createtime','desc')->limit('10')->get();
        $incs_a = [];
        if($interest) {
            foreach($interest as $in) {
                if(Article::where('cid',$in)->exists())array_push($incs_a,Article::where('id',(Redis::ZREVRANGE('article:classification:temperature:rank:'.$in,0,1)[0]))->with('articleAvatar')->with('classification')->first());
            }
            $articleNew = Article::whereIn('cid',$interest)->orderBy('created_at','desc')->with('classification')->limit(20)->get();
        } else {
            $articleNew = Article::orderBy('created_at','desc')->with('classification')->limit(20)->get();
        }
        $my = Article::where('manage_id',$id)->orderBy('updated_at','desc')->limit(6)->get(['id','title','updated_at']);

        $data = [
            'sc_id' => $user->specialty,
            'specialty' => $user->getSpecialty,
            'in_id' => count($interest)?$interest[0]:0,
        	'recommend' 	=> $a,
        	'articles' 		=> $articleNew,
            'as'    => $as,
            'debates' => $debates,
            'reviews' => $reviews,
            'resorts' => $resorts,
            'my' => $my,
            'announcements' => $announcements,
        ];
    	return json_encode($data);
    }
}
