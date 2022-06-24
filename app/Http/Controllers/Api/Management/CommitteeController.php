<?php

namespace App\Http\Controllers\Api\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\Committee;
use App\Models\Committee\CommitteeDocument;
use App\Models\Committee\Surveillance\SurveillanceRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleRecord;
use App\Models\Committee\Surveillance\SurveillanceExamRecord;
use App\Models\Committee\Surveillance\SurveillanceMark;
use App\Models\Committee\Surveillance\SurveillanceArticleMark;
use App\Models\Committee\Surveillance\SurveillanceExamMark;
use App\Models\Committee\Surveillance\SurveillanceWarning;
use App\Models\Committee\Surveillance\SurveillanceArticleWarning;
use App\Models\Committee\Surveillance\SurveillanceExamWarning;
use App\Models\Committee\Surveillance\GroupMark;
use App\Models\Committee\Surveillance\GroupWarning;
use App\Models\Committee\Surveillance\SurveillanceMarkType;
use App\Models\Personnel\Role\RoleApplyRecord;
use App\Models\Personnel\Role\RoleElectRecord;
use App\Home\Personnel\Inform;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\JudgementInform;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CommitteeController extends Controller
{
    //
    public function all_committees() {
        $committees = Committee::all();
        $user = auth('api')->user()->with('getRole')->with('getCommittee')->first();
        return $re = [
            'committees' => $committees,
            'user'      => $user,
        ];
    }
    public function committees() {
        // 得到管理组
    	$committees = Committee::all();
        $user = auth('api')->user()->with('getRole')->with('getCommittee')->first();
        // 得到巡查信息、主内容操作信息、举报信息
        $surveillances_entry = SurveillanceRecord::where('status',0)->orderBy('created_at','desc')->with('author')->with('content')->limit(9)->get();
        $surveillances_article = SurveillanceArticleRecord::where('status',0)->orderBy('created_at','desc')->with('author')->with('content')->limit(9)->get();
        $surveillances_exam = SurveillanceExamRecord::where('status',0)->orderBy('created_at','desc')->with('author')->with('content')->limit(9)->get();
        $surveillances = [
            'entry'=> $surveillances_entry,
            'article'=> $surveillances_article,
            'exam'=> $surveillances_exam,
        ];
        $marks_entry = SurveillanceMark::where('status',0)->with('author')->with('content')->with('dispose')->orderBy('created_at','desc')->limit(9)->get();
        $marks_article = SurveillanceArticleMark::where('status',0)->with('author')->with('content')->with('dispose')->orderBy('created_at','desc')->limit(9)->get();
        $marks_exam = SurveillanceExamMark::where('status',0)->with('author')->with('content')->with('dispose')->orderBy('created_at','desc')->limit(9)->get();
        $marks_group = GroupMark::where('status',0)->with('author')->with('content')->with('dispose')->orderBy('created_at','desc')->limit(9)->get();
        $markTypes = SurveillanceMarkType::all();
        $marks = [
            'entry'=> $marks_entry,
            'article'=> $marks_article,
            'exam'=> $marks_exam,
            'group'=> $marks_group
        ];
        $warnings_entry = SurveillanceWarning::where('status',0)->with('author')->with('content')->orderBy('createtime','desc')->limit(9)->get();
        $warnings_article = SurveillanceArticleWarning::where('status',0)->with('author')->with('content')->orderBy('createtime','desc')->limit(9)->get();
        $warnings_exam = SurveillanceExamWarning::where('status',0)->with('author')->with('content')->orderBy('createtime','desc')->limit(9)->get();
        $warnings_group = GroupWarning::where('status',0)->with('author')->with('content')->orderBy('createtime','desc')->limit(9)->get();
        $warnings = [
            'entry'=> $warnings_entry,
            'article'=> $warnings_article,
            'exam'=> $warnings_exam,
            'group'=> $warnings_group,
        ];
        // 主内容操作信息还没做呢
        // 举报信息
        $basicInforms = Inform::orderBy('created_at','desc')->with('author')->limit('9')->get();
        $messageInforms = MessageInform::orderBy('created_at','desc')->with('author')->limit('9')->get();
        $judgementInforms = JudgementInform::orderBy('created_at','desc')->with('author')->limit('9')->get();
        $informs = [
            'basicInforms'=> $basicInforms,
            'messageInforms'=> $messageInforms,
            'judgementInforms'=> $judgementInforms,
        ];
        // 权限信息
        $roleApplies = RoleApplyRecord::where('status',0)->with('author')->with('role')->orderBy('createtime','desc')->limit(10)->get();
        $roleElects = RoleElectRecord::where('status',0)->with('author')->with('elector')->with('role')->orderBy('createtime','desc')->limit(10)->get();
        $roleMsgs = [
            'roleApplies'=> $roleApplies,
            'roleElects'=> $roleElects
        ];
    	return $re = [
            'committees' => $committees,
            'marks' => $marks,
            'types' => $markTypes,
            'warnings' => $warnings,
    		'surveillances' => $surveillances,
            'informs'      => $informs,
            'roleMsgs'      => $roleMsgs,
            'user'      => $user,
    	];
    }

    public function committee(Request $request) {
        $data = $request->data;
        $user = Auth::user()->with('getRole')->first();
        $id = $data['id'];
        if($id==0)$id = $user->getCommittee->id;
        $cid = $data['cid'];
        $title = $data['title'];
        $committee = $manager = $members = $documents = '';
    	if($id>0) {
            $committee = Committee::where('id',$id)->with('manager:id,username')->with('creator:id,username')->with('members:committee_id,id,username')->with('documents:tcid,id,title')->first();
            $manager= User::where('id',$committee->manage_id)->with('getAvatar')->first();
            $members= User::where('committee_id',$id)->with('getAvatar')->with('getRole')->orderBy('role_id')->get();
            $documents = CommitteeDocument::where('tcid',$id)->orderBy('created_at','desc')->get();
        }
    	return $re = [
            'committee' => $committee,
            'manager' => $manager,
            'members' => $members,
            'documents' => $documents,
    		'user' => $user,
    	];
    }

    // zhuyi 启用
    public function managerUpdate(Request $request) {
        $id = $request->id;
        $cid = $request->cid;
        $user = auth('api')->user()->with('getRole')->first();
        $result = Committee::managerUpdate($id, $user->id);
        $committee = Committee::where('id',$id)->with('manager')->with('members')->with('documents')->first();
        $manager= User::where('id',$committee->manage_id)->with('getAvatar')->first();
        $members= User::where('committee_id',$id)->with('getAvatar')->with('getRole')->orderBy('role_id')->get();
        $documents = CommitteeDocument::where('tcid',$id)->orderBy('created_at','desc')->get();
        return $re = [
            'success' => $result?true:false,
            'committee' => $committee,
            'manager' => $manager,
            'members' => $members,
            'documents' => $documents,
            'user' => $user,
        ];
    }

    public function committeeCreate(Request $request) {
    	$data = $request->data;
    	$user = auth('api')->user();
    	$title = $data['title'];
    	$tcid = $data['tcid'];
        $scid = $data['scid'];
    	$thcid = $data['thcid'];
        $cid = $data['cid'];
    	$hierarchy = $data['hierarchy'];
    	$introduction = $data['introduction'];
        $cs = '';
    	// return $data;
    	if(count($data['path'])>1){
			$pathD = $data['path'];
			for($i=0;$i<count($pathD)-1;$i++){
				Storage::disk('public')->delete($pathD[$i]);
			}
		}
		$path = '/storage/' . end($data['path']);
        // 这里不会默认管理员，管理员需要在创建后，因为创建者不一定会受专业限制
    	$result = Committee::newCommittee($title,$tcid,$scid,$thcid,$cid,$hierarchy,$path,$introduction,$user->id);
        if($result)$cs=Committee::all();
    	return [
    		'success' => $result?true:false,
            'committees'=>$cs
    	];
    }
}
