<?php

namespace App\Http\Controllers\Api\Personal\MyInform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Inform;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\JudgementInform;

class MyInformController extends Controller
{
    //显示我的举报信息，举报信息除了作者和有该处理权限的管理员外都无法查看
    public function myInforms(){
    	$basicInforms = Inform::orderBy('created_at','desc')->with('getTarget')->limit('20')->get();
        $messageInforms = MessageInform::orderBy('created_at','desc')->with('getTarget')->limit('20')->get();
    	$judgementInforms = JudgementInform::orderBy('created_at','desc')->with('getTarget')->limit('20')->get();
    	return array(
    		'basicInforms' 	=> $basicInforms,
    		'messageInforms' 	=> $messageInforms,
    		'judgementInforms' 	=> $judgementInforms
    	);
    }
}
