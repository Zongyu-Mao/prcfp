<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Announcement;
use App\Home\Personnel\MedalSuit;

class MedalSuitController extends Controller
{
    //显示功章页面，这个是功章首页medalSuits
    public function medalSuit(){
    	$announcements = Announcement::where('scope','6')->orderBy('createtime','desc')->limit('10')->get();
    	$medalSuits = MedalSuit::orderBy('created_at','desc')->with('getMedals')->get();
        
		return array(
    		'announcements'	=> $announcements,
            'medalSuits'    => $medalSuits
    	);
    }

    //详情功章
    public function medalSuitDetail(Request $request){
    	$id = $request->id;
    	$title = $request->title;
    	$medalSuit = MedalSuit::where('id',$id)->with('getMedals')->first();
    	if($medalSuit->title == $title) {
    		return array(
	    		'medalSuit'	=> $medalSuit
	    	);
    	}
    }
}
