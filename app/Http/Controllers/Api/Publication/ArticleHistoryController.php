<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleDynamic;

class ArticleHistoryController extends Controller
{
    //显示著作的历史（动态）
    public function articleHistory(Request $requst,$id,$title){
    	$article = Article::find($id);
    	$dynamics = ArticleDynamic::where('aid',$id)->orderBy('createtime','DESC')->limit(20)->get();
    	return $dynamics;
    }
}
