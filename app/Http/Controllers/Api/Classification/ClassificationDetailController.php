<?php

namespace App\Http\Controllers\Api\Classification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Classification;
use App\Home\Announcement;

class ClassificationDetailController extends Controller
{
    //展示具体的分类页面
    public function underclass(Request $request){
    	// 该分类所有的词条、著作、试卷和组织,一般展示我们都不与兴趣专业挂钩
    	$id = $request->id;
    	$classname = $request->classname;
    	$classification = Classification::find($id);
    	if($classification->level==4 && $classname==$classification->classname){
    		$entries = Entry::where('cid',$id)->limit(40)->get();
	    	$articles = Article::where('cid',$id)->limit(40)->get();
	    	$exams = Exam::where('cid',$id)->limit(40)->get();
	    	$groups = Group::where('cid',$id)->limit(40)->get();
    	}
    	return array(
    		'entries'	=> $entries,
    		'articles'	=> $articles,
    		'exams'		=> $exams,
    		'groups'	=> $groups,
    		'classification'	=> $classification,
    	);
    	
    }
    //中层分类显示
    public function middleclass(Request $request){
    	$id = $request->id;
    	$classname = $request->classname;
    	$classification = Classification::where('id',$id)->with('allClassification')->first();
    	$announcements = Announcement::where('scope','5')->orderBy('createtime','desc')->limit('10')->get();
    	return array(
    		'announcements'	=> $announcements,
    		'classification'	=> $classification,
    	);
    }
}
