<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personal\UserPicture;
use App\Home\Personal\UserClass;
use App\Home\Classification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;

class PersonalSetController extends Controller
{
    public function setting(Request $request) {
        $user = auth('api')->user();
    	$id = $user->id;
    	// 获得兴趣专业
		$data = $user->with('getSpecialty')->with('getInterest')->with('getAvatar')->with('getMyManageGroups')->first();
		return $data = array(
			'user' => $data,
		);
    }

    //设置个人主页
    public function personalSet(Request $request){
        $user = auth('api')->user();
		$personalInfo = $request->userInfo;
		$countUpdate = 0;
		foreach($personalInfo as $k => $v) {
			if(User::where('id',$user->id)->update([
				$k => $v
			]))$countUpdate++;
		}
		return $countUpdate;
    }

    // 得到兴趣专业
    public function getSpecialities(Request $request){
    	$id = $request->id;
    	$data = auth('api')->user();
    	$interestArr = UserClass::where('user_id',$data->id)->pluck('class_id')->toArray();
    	$classification = Classification::where('pid',0)->with('allClassification')->get();
    	return $data = array(
    		'classification' => $classification,
            'interestArr' => $interestArr,
            'specialty' => $data->specialty,
    		'data' => $data
    	);
    }

    // 变更兴趣专业
    public function specialityModify(Request $request){
    	$stand = $request->stand;
        $user = auth('api')->user();
        $user_id = $user->id;
        $speArr = $request->speciality;
        $result = false;
        $res = 0;
        $back = '';
        $interestArr = UserClass::where('user_id',$user_id)->pluck('class_id')->toArray();
        if($stand==1||$stand==2){
            
            
            // 实施添加或删除
            for($i=0;$i<count($speArr);$i++){
                if($stand==1 && !in_array($speArr[$i], $interestArr) && $speArr[$i]!=$user->specialty){
                    $result = UserClass::classAdd($user_id,$speArr[$i]);
                    $res++;
                }
            }
            for($i=0;$i<count($speArr);$i++){
                if($stand==2 && in_array($speArr[$i], $interestArr)){
                    $result = UserClass::classDelete($user_id,$speArr[$i]);
                    $res++;
                }
            }
        }else if ($stand==3) {
            if(!$user->specialty){
               $result = User::specialtyAdd($user->id,$speArr[0]);
            }
            $result ? $res++ : 0;
        }else if ($stand==4) {
            if(in_array($speArr[0],$interestArr)){
                UserClass::classDelete($user_id,$speArr[0]);
            }
            if($user->specialty){
               $result = User::specialtyAdd($user->id,$speArr[0]);
            }

            $result ? $res++ : 0;
        }
    	if($result){
            $back = $user->getInterest;
            $spe = ($stand==4?User::find($user_id)->getSpecialty:'');//这里不得不适用User，用auth只能得到原来的user模型
        }
    	return [
    		'success'	=> $result? true:false,
            'count'     => $res,
            'back'      => $back,
    		'specialty'		=> $spe,
    	];
    }
}
