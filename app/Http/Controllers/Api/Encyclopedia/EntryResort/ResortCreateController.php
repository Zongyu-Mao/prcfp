<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryResort;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ResortCreateController extends Controller
{
    //求助内容的创建
    public function resort_create(Request $request){
        $id = $request->eid;
    	$cid = $request->cid;
    	$entryTitle = $request->entry_title;
    	$result = false;
		$title = $request->title;
		$content = $request->resort;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
		//求助的有效期是30天
        $deadline = Carbon::now()->addDays(30);

    	//将反对内容写入反对讨论表
    	$result = EntryResort::resortAdd($id,$cid,0,$deadline,$title,$content,$author,$author_id,$entryTitle);
    	
        //返回结果
        return ['success' => $result? true:false];
    }
}
