<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationDiscussion;
use App\Home\Publication\ArticleCooperation\ArticleCooperationDiscussion;
use App\Home\Examination\ExamCooperation\ExamCooperationDiscussion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CooperationDiscussionController extends Controller
{
    public function discussion(Request $request){

        //接收改过的任务描述并写入数据表
        $comment = $request->discussion;
        $id = $request->id;
        $scope = $request->scope;
        $result = false;
        $ds = '';
        // return $data1;
        if($comment){
        	if($scope==1){
        		$result = EntryCooperationDiscussion::discussionAdd($id,$comment,auth('api')->user()->id,auth('api')->user()->username);
                $ds = EntryCooperationDiscussion::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
        	}elseif($scope==2){
        		$result = ArticleCooperationDiscussion::discussionAdd($id,$comment,auth('api')->user()->id,auth('api')->user()->username);
                $ds = ArticleCooperationDiscussion::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
        	}elseif($scope==3){
                 $result = ExamCooperationDiscussion::discussionAdd($id,$comment,auth('api')->user()->id,auth('api')->user()->username);
                 $ds = ExamCooperationDiscussion::where('cooperation_id',$id)->orderBy('created_at','desc')->limit(15)->get();
            }  
        }
        return ['success'=>$result ? true : false,'discussions'=>$ds];
    }
}
