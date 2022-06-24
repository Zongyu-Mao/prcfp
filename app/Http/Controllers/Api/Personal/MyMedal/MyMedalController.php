<?php

namespace App\Http\Controllers\Api\Personal\MyMedal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Medal;
use Illuminate\Support\Facades\Auth;

class MyMedalController extends Controller
{
    //我的功章，这里显示的是我创建的功章
    public function myMedals(){
    	$user = auth('api')->user();
    	$medals = Medal::where('creator_id',$user->id)->orderBy('suit_id','asc')->orderBy('sort','asc')->paginate(5);
    	return array(
    		'medals' 	=> $medals
    	);
    }
}
