<?php

namespace App\Http\Controllers\Api\Personal\MyWorks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use Illuminate\Support\Facades\Auth;

class myArticleController extends Controller
{
    //展示我的著作
    public function myArticles(Request $request){
        $user = auth('api')->user();
    	$user_id = $user->id;
    	// 我的自管理词条
    	$manageArticles = Article::where('manage_id',$user_id)->orderBy('created_at','desc')->get();
        $manageCooeprationIds = array_filter($manageArticles->pluck('cooperation_id')->toArray());
        // 我的自管理协作计划
        $manageCooperations = ArticleCooperation::where('id',$manageCooeprationIds)->with('getArticle')->orderBy('created_at','desc')->get();
        // 我的普通协作
        $cooperationIds = ArticleCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();
        $normalCooperations = ArticleCooperation::whereIn('id',$cooperationIds)->with('getArticle')->orderBy('created_at','desc')->get();
        // 我的求助
        $myResorts = ArticleResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->get();
        // 我的评审
        $myReviews = ArticleReview::where('initiate_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->get();
        
        // 我的攻辩
        $myDebates = ArticleDebate::where('Aauthor_id',$user_id)->orWhere('Bauthor_id',$user_id)->orWhere('referee_id',$user_id)->with('getContent')->orderBy('created_at','desc')->get();

        // 我的反对
        $myOpponents = ArticleOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->get();
        // 我的建议
        $myAdvises = ArticleAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->get();
        
        return $data = array(
        	'articles' => $manageArticles,
        	'm_cooperations' => $manageCooperations,
        	'n_cooperations' => $normalCooperations,
        	'resorts' => $myResorts,
        	'reviews' => $myReviews,
        	'debates' => $myDebates,
        	'opponents' => $myOpponents,
        	'advises' => $myAdvises
        );
    }
}
