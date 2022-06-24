<?php

namespace App\Http\Controllers\Api\Organization\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use App\Home\Organization\Group\GroupVote;
use App\Home\Organization\Group\GroupVoteRecord;
use App\Home\Organization\Group\GroupEvent;
use App\Home\UserDynamic;
use App\Models\User;

class GroupUserController extends Controller
{
    //组织成员页的显示
    public function groupUser(Request $request){
    	$id = $request->id;
    	$title = $request->name;
    	$data = Group::find($id);
    	$crewArr = $data->members()->pluck('user_id')->toArray();
        $members = $crewArr;
        array_push($members,$data->manage_id);
    	$userDynamics = UserDynamic::whereIn('user_id',$members)->orderBy('createtime','desc')->limit('30')->get();
    	$data_votes_count = GroupVote::where([['gid',$id],['status','0']])->exists() ? '1':'0';
    	$data_votes = GroupVote::where([['gid',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
        $vote_history=GroupVote::where([['gid',$id],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
        $groupMembers = GroupUser::where('gid',$id)->get();
        $crews = User::whereIn('id',$members)->with('getAvatar')->get();
        $events = GroupEvent::where('gid',$id)->limit(10)->orderBy('created_at','desc')->get();
    	return array(
     		'crewArr'	=> $members,
     		'crews'	=> $crews,
     		'group'	=> $data,
     		'votes'	=> $data_votes,
            'vote_history'  => $vote_history,
     		'events'	=> $events,
     		'userDynamics'	=> $userDynamics
     	);

    }
}
