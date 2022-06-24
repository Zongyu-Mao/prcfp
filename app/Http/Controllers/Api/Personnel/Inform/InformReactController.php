<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Inform;
use App\Home\Personnel\Inform\InformOperateRecord;
use App\Home\Personnel\JudgementInform;
use App\Home\Personnel\JudgementInform\JudgementInformOperateRecord;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\MessageInform\MessageInformOperateRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class InformReactController extends Controller
{
    //通过举报信息
    public function react(Request $request){
    	$id = $request->id;
    	$standpoint = $request->stand;
    	$scope = $request->scope;
    	$user_id = auth('api')->user()->id;
    	$operator_id = $user_id;
    	$createtime = Carbon::now();
    	$user = auth('api')->user();
    	$user_type = $user->getRole->sort;
    	$result = false;
        // 举报反馈后，记录会变、举报本身的状态也可能会变
        $rs = '';
    	if($scope==1){
    		$inform = Inform::find($id);
    		if($user_type > 1 && !InformOperateRecord::where([['inform_id',$id],['operator_id',$user_id]])->exists()){
    			$result = InformOperateRecord::informOperateRecordAdd($id,$operator_id,$standpoint,$createtime);
    		}
            if($result) {
                $inform = Inform::where('id',$id)->with('author')->with('getTarget')->first();
                $rs = InformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
            }
    	}elseif($scope==2){
    		$inform = JudgementInform::find($id);
    		if($user_type > 1 && !JudgementInformOperateRecord::where([['inform_id',$id],['operator_id',$user_id]])->exists()){
    			$result = JudgementInformOperateRecord::informOperateRecordAdd($id,$operator_id,$standpoint,$createtime);
    		}
            if($result) {
                $inform = JudgementInform::where('id',$id)->with('author')->with('getTarget')->first();
                $rs = JudgementInformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
            }
    	}elseif($scope==3){
    		$inform = MessageInform::find($id);
    		if($user_type > 1 && !MessageInformOperateRecord::where([['inform_id',$id],['operator_id',$user_id]])->exists()){
    			$result = MessageInformOperateRecord::informOperateRecordAdd($id,$operator_id,$standpoint,$createtime);
    		}
            if($result) {
                $inform = MessageInform::where('id',$id)->with('author')->with('getTarget')->first();
                $rs = MessageInformOperateRecord::where('inform_id',$id)->with('getOperator')->get();
            }
    	}
    	return [
    		'success'	=>  $result? true:false,
            'records'   =>  $rs,
            'inform'    =>  $inform
    	];

    }

    //驳回举报信息
    public function rejectInform(Request $request){
    	$inform_id = $request->input('id');
    	$inform = JudgementInform::find($inform_id);
    	
    	if(auth('api')->user()->getRole->getRole->type == 0 || auth('api')->user()->getRole->getRole->type > 1 && !JudgementInformOperateRecord::where([['inform_id',$inform_id],['operator_id',auth('api')->id()]])->exists()){
			$operator_id = auth('api')->user()->id;
			$standpoint = 2;
			$createtime = Carbon::now();
			$result = JudgementInformOperateRecord::informOperateRecordAdd($inform_id,$operator_id,$standpoint,$createtime);
    	}else{
    			$result = 0;
    	}
    	return $result ? 1:0;
    }
}
