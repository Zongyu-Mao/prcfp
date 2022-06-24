<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Milestone;
use App\Home\Announcement;

class MilestoneController extends Controller
{
    //显示里程碑页面
    public function milestones(){
    	$announcements = Announcement::where('scope','6')->orderBy('createtime','desc')->limit('10')->get();
    	$milestones = Milestone::orderBy('sort','asc')->get();
    	return array(
    		'announcements'	=> $announcements,
    		'milestones'	=> $milestones
    	);
    }
}
