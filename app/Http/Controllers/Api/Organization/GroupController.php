<?php

namespace App\Http\Controllers\Api\Organization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Classification;
use App\Home\Organization\Group\GroupEmblem;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Models\Committee\Surveillance\GroupMark;
use App\Models\Committee\Surveillance\GroupWarning;

class GroupController extends Controller
{
    //显示组织详情页
    public function group(Request $request){
    	$id = $request->id;
    	$title = $request->title;
    	$data = Group::where('id',$id)->with('groupEmblem')->with('classification')->first();
        Redis::INCR('group:views:'.$data->id);
        Redis::INCR('group:temperature:'.$data->id);
        // 更新排行榜热度,总榜
        Redis::ZINCRBY('group:temperature:rank',1,$data->id);
        // 分类榜
        Redis::ZINCRBY('group:classification:temperature:rank:'.$data->cid,1,$data->id);
        // 分类顺序榜
        Redis::ZINCRBY('classification:temperature:rank',1,$data->cid);
        // 此处热度是在Redis下，没有在Cache下
        $temperature = Redis::GET('group:temperature:'.$data->id);
		$cid = $data->cid;
		$docs = $data->groupDocs;
		$user = auth('api')->user()->id;
		$focus = $data->groupFocus()->pluck('user_id');
 		$data_class = Classification::getClassPath($cid);
        $user = auth('api')->user();
        $role = $user->getRole;
        $committee = $user->getCommittee;
        $user_id = $user->id;

        $marks = GroupMark::where('sid',$id)->get()??[];
        $warnings = GroupWarning::where('sid',$id)->get()??[];

        $crewArr = $data->members()->pluck('user_id')->toArray();
        $members = $crewArr;
        array_push($members,$data->manage_id);

     	return array(
     		'class_path'	=> $data_class,
     		'group'	=> $data,
     		'docs'	=> $docs,
            'focus' => $focus,
            'user' => $user,
     		'crewArr'	=> $members,
            'marks' => $marks,
            'warnings' => $warnings,
     	);
    }
}
