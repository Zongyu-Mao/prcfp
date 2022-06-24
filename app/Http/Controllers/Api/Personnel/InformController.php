<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Inform;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\JudgementInform;

class InformController extends Controller
{
    //举报页面,展示所有的举报信息
    public function informs(Request $request){
        $basicInforms = Inform::orderBy('created_at','desc')->with('author')->limit('20')->get();
        $messageInforms = MessageInform::orderBy('created_at','desc')->with('author')->limit('20')->get();
    	$judgementInforms = JudgementInform::orderBy('created_at','desc')->with('author')->limit('20')->get();
    	return array(
    		'basicInforms'		=> $basicInforms,
    		'messageInforms'		=> $messageInforms,
    		'judgementInforms'		=> $judgementInforms
    	);
    }
}
