<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Level;
use App\Home\Announcement;

class LevelController extends Controller
{
    //显示等级页面
    public function levels(){
    	$announcements = Announcement::where('scope','6')->orderBy('createtime','desc')->limit('10')->get();
    	$levels = Level::orderBy('sort','asc')->get();
    	return array(
    		'announcements'	=> $announcements,
    		'levels'	=> $levels
    	);
    }
}
