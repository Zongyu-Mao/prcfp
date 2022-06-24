<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDoc;
use App\Home\Organization\Group\GroupUser;
use App\Home\Classification;
use App\Home\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class OrganizationController extends Controller
{
    //组织的首页
    public function organization(){
    	// （本设定已经取消）组织的首页，展示兴趣内推荐组织、新晋组织、优秀文档、最新文档
        // 组织排行仍然会有，不过不再是主内容
        // 新的设定，进入组织首页展示的是我的组织，我的组织包括我的主组织，此是在user表内，我的兴趣组织，此是在groupusers表内，目前不新增表格
        // 这里有manage_id和user的gid重复的现象，所以做出规定：
        // user的gid是user的主组织，但不一定是manager，但是manager的gid一定是与所manage的gid一致，
        // 即主专业下只能有唯一的manage组织，manage的组织一定是主专业组织，但是参与的不作限制
        $user = auth('api')->user();
    	$id = $user->id;
        $specialty = $sc_id = $user->specialty;

        $myGroup = '';
        $myGroupDocs = '';
        $igroups = '';
        $gid = $user->gid;
        if($gid!=0) {
            $myGroup = Group::where('id',$gid)->with('groupEmblem')->with('classification')->first();
            $myGroupDocs = GroupDoc::where('gid',$gid)->orderBy('created_at','desc')->limit(8)->get();
        }
        $ig_ids = GroupUser::where('user_id',$id)->pluck('gid')->toArray();
        $igroups = Group::whereIn('id',$ig_ids)->orderBy('created_at','desc')->get();
        // xingqu
        $interest = $user->getInterest->pluck('id')->toArray();
        $nosc = false;
        if($sc_id)array_push($interest,$sc_id);
        if($sc_id==0 && count($interest)>0){
            $sc_id=$interest[0];
        } else {
            $nosc = !$nosc;
            $sc_id = 38;//注意这里专业排行还没有 classificationTRank
        }
        $rec = Group::where('id',Redis::ZREVRANGE('group:classification:temperature:rank:'.$sc_id,0,1)[0])->with('groupEmblem')->with('classification')->first();
        $gs = Group::whereIn('id',Redis::ZREVRANGE('group:classification:temperature:rank:'.$sc_id,1,3))->get(['id','title']);

        $incs_e = [];
        if($interest) {
            foreach($interest as $in) {
                if(Group::where('cid',$in)->exists())array_push($incs_e,Group::where('id',(Redis::ZREVRANGE('group:classification:temperature:rank:'.$in,0,1)[0]))->with('groupEmblem')->with('classification')->first());
            }
            $groups = Group::whereIn('cid',$interest)->orderBy('created_at','desc')->with('classification')->limit(20)->get();
        } else {
            $groups = Group::orderBy('created_at','desc')->with('classification')->limit(20)->get();
        }

        $interestGroup=Group::whereIn('cid',$interest)->pluck('id')->toArray();
        $docRecommend = GroupDoc::find(1);
        $groupDocs = GroupDoc::whereIn('gid',$interestGroup)->limit(10)->get();
        $class = Classification::all();
    	$announcements = Announcement::where('scope',4)->orderBy('createtime','desc')->limit(20)->get();
    	return $data = [
            'groupRecommend'    => $rec,
        	'specialty' 	=> $specialty,
            'docRecommend'  => $docRecommend,
            'myGroup'  => $myGroup,
            'myGroupDocs'  => $myGroupDocs,
            'igroups'  => $igroups,
        	'gs' 	=> $gs,
            'groups'        => $groups,
            'interest'        => $interest,
            'incs_e'        => $incs_e,
        	'interestGroup' 		=> $interestGroup,
        	'docs' 		=> $groupDocs,
        	'announcements'	=> $announcements,
        ];
    }
}
