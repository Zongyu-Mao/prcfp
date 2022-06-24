<?php

namespace App\Http\Controllers\Api\Announcement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Announcement;
use App\Models\Globalization\GlobalNotification;

class AnnouncementController extends Controller
{
    //
    public function announcements(Request $request) {
    	$data = $request->data;
    	$type = $data['type'];
        $ats = '';
    	if($type==0){
    		$ats = Announcement::orderBy('createtime','desc')->limit(20)->get();
    	}else {
    		$ats = Announcement::where('scope',$type)->orderBy('createtime','desc')->limit(20)->get();
    	}

    	return $announcements = ['announcements'=>$ats];
    }
}
