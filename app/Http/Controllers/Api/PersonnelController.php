<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Level;
use App\Home\Announcement;

class PersonnelController extends Controller
{
    //展示人事首页
    public function personnel(){
    	// 人事包含举报信息、等级、里程碑、道具等
    	$announcements = Announcement::where('scope',6)->orderBy('createtime','desc')->limit(20)->get();
    	return $return = array(
    		'announcements'	=> $announcements
    	);
    }
}
