<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage;
use App\Home\Publication\ArticleCooperation\ArticleCooperationMessage;
use App\Home\Examination\ExamCooperation\ExamCooperationMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CooperationMessageController extends Controller
{
    // 处理用户在协作面板的留言
    public function message(Request $request){
        //接收留言内容并写入数据表
        $id = $request->id;
        $title = $request->title;
        $message = $request->message;
        $result = false;
        $scope = $request->scope;
        $messages = '';
        // return $data1;
        if($title && $message){
        	if($scope==1){
        		$result = EntryCooperationMessage::MessageAdd($id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = EntryCooperationMessage::where([['cooperation_id',$id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
        	}elseif($scope==2){
				$result = ArticleCooperationMessage::MessageAdd($id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = ArticleCooperationMessage::where([['cooperation_id',$id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
        	}elseif($scope==3){
                $result = ExamCooperationMessage::MessageAdd($id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = ExamCooperationMessage::where([['cooperation_id',$id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
            }
            
        }
        return ['success'=>$result? true:false,'messages'=>$messages];
    }

    //处理协作组对用户留言的回复
    public function message_reply(Request $request){
    	// 补充逻辑： 是否协作组成员，并验证title、message数据
    	// 此id是message的id
    	$id = $request->msg_id;
    	$scope = $request->scope;
    	// cooperation_id也直接传参过来，但是要验证一下
        $cooperation_id = $request->cooperation_id;
        $result = false;
        //接收留言内容并写入数据表
        $title = $request->title;
        $message = $request->message;
        // return $data1;
        if($title && $message){
        	if($scope==1){
        		$result = EntryCooperationMessage::MessageReply($cooperation_id,$id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = EntryCooperationMessage::where([['cooperation_id',$cooperation_id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
        	}elseif($scope==2){
        		$result = ArticleCooperationMessage::MessageReply($cooperation_id,$id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = ArticleCooperationMessage::where([['cooperation_id',$cooperation_id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
        	}elseif($scope==3){
                $result = ExamCooperationMessage::MessageReply($cooperation_id,$id,$title,$message,auth('api')->user()->id,auth('api')->user()->username);
                $messages = ExamCooperationMessage::where([['cooperation_id',$cooperation_id],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
            }
            
        }
        return ['success'=>$result? true:false,'messages'=>$messages];
    }
}
