<?php

namespace App\Http\Controllers\Api\Management\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Home\Personnel\Role;
use App\Models\Management\Role\RoleJudgeRecord;
use App\Models\Committee\Committee;
use Carbon\Carbon;

class CommitteeMemberController extends Controller
{
    //
    public function judgeRole(Request $request) {
    	$data = $request->data;
        $hander = auth('api')->user();
    	$committee_id = $data['committee_id'];
    	$judge_id = $data['judge_id'];
    	$result = false;
        $createtime = Carbon::now();
    	// panduan
    	$role_id = Role::where('sort',1)->first()->id;
    	$result = User::judgeRole($judge_id,$role_id,0);
        $members='';
        // 添加judge记录
        $result = RoleJudgeRecord::recordAdd($role_id,$judge_id,$hander->id,$createtime);
        if($result)$members= User::where('committee_id',$committee_id)->with('getAvatar')->with('getRole')->orderBy('role_id')->get();
    	return [
    		'success' => $result?true:false,
            'members'   =>  $members
    	];
    }

    // 成员退出辞任，回复普通身份
    public function memberQuit(Request $request) {
        $data = $request->data;
        $user = auth('api')->user();
        $committee_id = $data['committee_id'];
        $user_id = $data['user_id'];
        $result = false;
        $createtime = Carbon::now();
        // panduan
        $role_id = Role::where('sort',1)->first()->id;
        // 同judge
        $result = User::judgeRole($user_id,$role_id,0);
        $members='';
        // 添加judge记录，如果handler是0就是辞任，不是就是强制退出了
        $result = RoleJudgeRecord::recordAdd($role_id,$user_id,0,$createtime);
        if($result)$members= User::where('committee_id',$committee_id)->with('getAvatar')->with('getRole')->orderBy('role_id')->get();
        return [
            'success' => $result?true:false,'members'=>$members
        ];
    }

    public function managerAward(Request $request) {
        $data = $request->data;
        $committee_id = $data['committee_id'];
        $user_id = $data['user_id'];
        $award_id = $data['award_id'];
        $manage_id = Committee::find($committee_id)->manage_id;
        $result = false;
        if($manage_id==auth('api')->user()->id && $manage_id==$user_id)$result = Committee::managerUpdate($committee_id,$award_id);
        if($result) {
            $committee = Committee::find($committee_id);
            $manager= User::where('id',$manage_id)->with('getAvatar')->first();
            $members= User::where('committee_id',$id)->with('getAvatar')->with('getRole')->orderBy('role_id')->get();
        }
        return [
            'success' => $result?true:false
        ];
    }
}
