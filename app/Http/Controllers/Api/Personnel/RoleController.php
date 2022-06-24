<?php

namespace App\Http\Controllers\Api\Personnel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Announcement;
use App\Home\Personnel\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\Committee\Committee;
use App\Models\User;
use App\Home\Classification;
use App\Models\Personnel\Role\RoleApplyRecord;
use App\Models\Personnel\Role\RoleElectRecord;

class RoleController extends Controller
{
    //显示角色页面
    public function roles(){
    	$announcements = Announcement::where('scope','5')->orderBy('createtime','desc')->limit('10')->get();
    	$roles = Role::orderBy('sort','asc')->get();
    	$user = auth('api')->user()->with('getRole')->with('getCommittee')->first();
        $ifApply = RoleApplyRecord::where([['user_id',$user->id],['status',0]])->exists();
        $ifElect = RoleElectRecord::where([['elect_id',$user->id],['status',0]])->exists();
        $ifElectOther = RoleElectRecord::where([['user_id',$user->id],['status',0]])->exists();
        // 模拟apply和elect
        // $cid = $user->specialty;
        // $thcid = Classification::find($cid)->pid;
        // $scid = Classification::find($thcid)->pid;
        // $tcid = Classification::find($scid)->pid;
        // $title = Classification::find($cid)->classname;
        // $hierarchy=4;
        // $committee_id = Committee::where('cid',$cid)->exists()?Committee::where('cid',$cid)->first()->id:Committee::newCommittee($title,$tcid,$scid,$thcid,$cid,$hierarchy,'path','introduction',1,1);
        // User::committeeUpdate($user->id,$committee_id);
    	return $data = array(
    		'announcements'	=>	$announcements,
    		'roles'			=>	$roles,
            'user'          =>  $user,
            'ifApply'       =>  $ifApply,
            'ifElect'       =>  $ifElect,
    		'ifElectOther'	=>	$ifElectOther,
    	);
    }
}
