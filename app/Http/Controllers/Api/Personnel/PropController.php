<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Announcement;
use App\Home\Personnel\Prop;

class PropController extends Controller
{
    //显示功章页面
    public function props(){
    	$announcements = Announcement::where('scope','6')->orderBy('createtime','desc')->limit('10')->get();
    	$props = Prop::orderBy('sort','asc')->get();
		return array(
    		'announcements'	=> $announcements,
    		'props'	=> $props
    	);
    }
}
