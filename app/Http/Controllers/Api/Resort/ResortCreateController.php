<?php

namespace App\Http\Controllers\Api\Resort;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Publication\ArticleResort;
use App\Home\Examination\ExamResort;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ResortCreateController extends Controller
{
    //求助内容的创建
    public function resort_create(Request $request){
        $id = $request->id;
    	$cid = $request->cid;
    	$scope = $request->scope;
        // return $request;
    	$result = false;
		$title = $request->title;
		$content = $request->resort;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
		//求助的有效期是30天
        $deadline = Carbon::now()->addDays(30);
        // return $request;
    	//将反对内容写入反对讨论表
    	if($scope==1){
    		$result = EntryResort::resortAdd($id,$cid,0,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = EntryResort::where([['eid',$id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
    	}elseif ($scope==2) {
            $result = ArticleResort::resortAdd($id,$cid,0,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = ArticleResort::where([['aid',$id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $result = ExamResort::resortAdd($id,$cid,0,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = ExamResort::where([['exam_id',$id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }
        //返回结果
        return ['success' => $result? true:false,'resorts'=>$resorts];
    }
}
