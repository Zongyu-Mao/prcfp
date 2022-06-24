<?php

namespace App\Http\Controllers\Api\Resort;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleResort\ArticleResortSupportComment;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamResort\ExamResortSupportComment;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ResortSupportController extends Controller
{
    //帮助内容的创建
    public function support(Request $request){
    	$id = $request->resort_id;
    	$content_id = $request->id;
        $scope = $request->scope;
    	$cid = $request->cid;
    	//此id是对应的求助id
    	$result = false;
    	// return $request;
            // $asking = EntryResort::where('id',$id)->first();
            // $askingid = $asking->author_id;
            // $askingname = $asking->author;
            // $askingname = $asking->author;
		$title = $request->title;
		$content = $request->support;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $resorts = '';
		//求助的有效期是30天
        $deadline = Carbon::now()->addDays(30);
        if($scope==1){
        	//将反对内容写入反对讨论表
    		$result = EntryResort::resortAdd($content_id,$cid,$id,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = EntryResort::where([['eid',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==2) {
            $result = ArticleResort::resortAdd($content_id,$cid,$id,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = ArticleResort::where([['aid',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $result = ExamResort::resortAdd($content_id,$cid,$id,$deadline,$title,$content,$author,$author_id);
            if($result){
                $resorts = ExamResort::where([['exam_id',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }
    	           
        //返回结果
		return ['success' => $result? true:false,'resorts'=>$resorts];
    }

    //帮助内容的拒绝,拒绝意见不再填入求助表，而是改放在评论表了
    public function support_reject(Request $request){
    	//此id是对应的帮助的id，也就是要拒绝的asking_id
    	$id = $request->resort_id;
    	$content_id = $request->id;
    	$scope = $request->scope;
    	$result = false;

		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $title = $request->title;
        $status = 2;
        $type = 1;
        $resorts = '';
    	//将反对内容写入评论表
    	if($scope==1){
    		$result1 = EntryResortSupportComment::rejectCommentAdd($content_id,$id,$comment,0,$title,$author_id,$author,$type);
	    	//更改帮助内容状态为被拒绝的status从0变为2,2代表拒绝
	        $result = EntryResort::resortSupportReject($id,$status);
            if($result){
                $resorts = EntryResort::where([['eid',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
    	}elseif ($scope==2) {
            $result1 = ArticleResortSupportComment::rejectCommentAdd($content_id,$id,$comment,0,$title,$author_id,$author,$type);
            //更改帮助内容状态为被拒绝的status从0变为2,2代表拒绝
            $result = ArticleResort::resortSupportReject($id,$status);
            if($result){
                $resorts = ArticleResort::where([['aid',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }elseif ($scope==3) {
            $result1 = ExamResortSupportComment::rejectCommentAdd($content_id,$id,$comment,0,$title,$author_id,$author,$type);
            //更改帮助内容状态为被拒绝的status从0变为2,2代表拒绝
            $result = ExamResort::resortSupportReject($id,$status);
            if($result){
                $resorts = ExamResort::where([['exam_id',$content_id],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }
    	
        //返回结果
		return ['success' => $result? true:false,'resorts'=>$resorts];
    }

    //处理帮助方案的接受机制
    public function support_accept(Request $request){
    	$id = $request->resort_id;
    	$scope = $request->scope;
    	$result = false;

		$user_id = auth('api')->user()->id;
        $resorts = '';
		// 确认一下操作者是不是求助者（感觉这个条件需要验证，但是又感觉不要，先这样放着）
        if($scope==1){
            $resort = EntryResort::find($id)->only('pid','eid','author_id');
        	$status = 1;
			//接受了帮助方案，更改帮助方案为已采纳，更改求助方案为解决
            if($user_id==EntryResort::find($resort['pid'])->author_id) {
                $result = EntryResort::resortSupportAccept($id,$status);
                if($result){
                    $resorts = EntryResort::where([['eid',$resort['eid']],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
                }
            }
			
        }elseif ($scope==2) {
            $resort = ArticleResort::find($id)->only('pid','aid','author_id');
            $status = 1;
            $result = ArticleResort::resortSupportAccept($id,$status);
            if($result){
                $resorts = ArticleResort::where([['aid',$resort['aid']],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }

        }elseif ($scope==3) {
            $resort = ExamResort::find($id)->only('pid','exam_id','author_id');
            $status = 1;
            $result = ExamResort::resortSupportAccept($id,$status);      
            if($result){
                $resorts = ExamResort::where([['exam_id',$resort['exam_id']],['pid','0'],['status','0']])->with('helpers')->orderBy('created_at','DESC')->get();
            }
        }
        return ['success' => $result? true:false,'resorts'=>$resorts];
    }
}
