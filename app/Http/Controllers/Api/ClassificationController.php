<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Announcement;
use Illuminate\Support\Facades\Cache;


class ClassificationController extends Controller
{
    //新建文章时显示内容的分类索引
    public function classification(){
    	$classification = Classification::where('pid','0')->with('allClassification')->get();
    	return array(
    		'classification' => $classification
    	);
    }
}
