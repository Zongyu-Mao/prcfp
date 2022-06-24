<?php

namespace App\Http\Controllers\Api\Personal\MyFocus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MyFocusController extends Controller
{
    //我的关注
    public function myFocuses(){
    	// 关注的词条、著作、试卷、组织、用户
    	$user = auth('api')->user();
    	$entries = $user->getFocusEntries;
    	$articles = $user->getFocusArticles;
    	$exams = $user->getFocusExams;
    	$users = $user->getFocusUsers;
    	return array(
    		'entries' 	=> $entries,
    		'articles' 	=> $articles,
    		'users' 	=> $users,
    		'exams' 	=> $exams
    	);
    }
}
