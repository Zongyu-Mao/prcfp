<?php

namespace App\Http\Controllers\Api\Personal\MyFocus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyCollectionController extends Controller
{
    //我的关注
    public function myCollections(){
    	// 关注的词条、著作、试卷、组织、用户
    	$user = auth('api')->user();
    	$entries = $user->getCollectEntries;
    	$articles = $user->getCollectArticles;
    	$exams = $user->getCollectExams;
    	return array(
    		'entries' 	=> $entries,
    		'articles' 	=> $articles,
    		'exams' 	=> $exams
    	);
    }
}
