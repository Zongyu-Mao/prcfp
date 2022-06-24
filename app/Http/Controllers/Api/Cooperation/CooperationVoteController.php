<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationVote;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationVoteRecord;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationVote;
use App\Home\Publication\ArticleCooperation\ArticleCooperationVoteRecord;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationVote;
use App\Home\Examination\ExamCooperation\ExamCooperationVoteRecord;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupVote;
use App\Home\Organization\Group\GroupVoteRecord;
use App\Home\Organization\Group\GroupUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CooperationVoteController extends Controller
{
    //申请加入协作计划
    public function join(Request $request){
    	$id = $request->cooperation_id;
    	$scope = $request->scope;
    	$title = $request->title;
        $content = $request->apply;
        $user = auth('api')->user()->id;
        $username = auth('api')->user()->username;
        $deadline = Carbon::now()->addDays(3);
        $type = 2;
        $partyids = '';
        $result=false;
        $votes = '';
        // 注意补充重复申请的防止*************************************************************
        if($scope==1){
        	//接收改过的任务描述并写入数据表
	        $data = EntryCooperation::find($id);
	        $crew = $data->crews()->pluck('user_id')->toArray();
	        $partynum = count($crew)+1;
	        // 每个用户个协作计划只能发起申请两次，所以check下
	        $check = EntryCooperationVote::where([['cooperation_id',$id],['type',2],['initiate_id',$user]])->count();
	        // 标题、内容不能为空，且同一个id仅能申请两次
	        if($content && $title && $user!=$data->initiate_id && !in_array($user, $crew) && $check<3){
	        	// 记录投票
	            $result = EntryCooperationVote::cooperationVote($id,$data->eid,$type,$deadline,$user,$username,$title,$content);
	        }
            // 申请不影响crew，仍然可以再次申请，只是影响了协作事件和投票
            if($result)$votes=EntryCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==2){
        	$data = ArticleCooperation::find($id);
        	$crew = $data->crews()->pluck('user_id')->toArray();
            // 每个用户个协作计划只能发起申请两次，所以check下
            $check = ArticleCooperationVote::where([['cooperation_id',$id],['type',2],['initiate_id',$user]])->count();
            $deadline = Carbon::now()->addDays(3);
            $partynum = count($crew)+1;
            $partyids = '';
            if($content && $title && $user!=$data->initiate_id && !in_array($user, $crew) && $check<3){
                $result = ArticleCooperationVote::cooperationVote($id,$data->aid,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=ArticleCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==3){
            $data = ExamCooperation::find($id);
            $crew = $data->crews()->pluck('user_id')->toArray();
            // 每个用户个协作计划只能发起申请两次，所以check下
            $check = ExamCooperationVote::where([['cooperation_id',$id],['type',2],['initiate_id',$user]])->count();
            $deadline = Carbon::now()->addDays(3);
            $partynum = count($crew)+1;
            $partyids = '';
            if($content && $title && $user!=$data->initiate_id && !in_array($user, $crew) && $check<3){
                $result = ExamCooperationVote::cooperationVote($id,$data->exam_id,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=ExamCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==4){
            $data = Group::find($id);
            $crew = $data->members()->pluck('user_id')->toArray();
            // 每个用户个协作计划只能发起申请两次，所以check下
            $check = GroupVote::where([['gid',$id],['type',2],['initiate_id',$user]])->count();
            $deadline = Carbon::now()->addDays(3);
            $partynum = count($crew)+1;
            $partyids = '';
            if($content && $title && $user!=$data->initiate_id && !in_array($user, $crew) && $check<3){
                $result = GroupVote::groupVote($id,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=GroupVote::where([['gid',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }
        return ['success'=>$result? true:false,'votes'=>$votes];
    }

    //协作计划小组内事务投票
    public function affairVote(Request $request){
    	$id = $request->cooperation_id;
    	$scope = $request->scope;
    	$user = auth('api')->user()->id;
        $username = auth('api')->user()->username;
        $title = $request->title;
        $content = $request->vote_content;
        $deadline = Carbon::now()->addDays(3);
        $type = 1;
        $result = false;
        $votes = '';
        if($scope==1){
			//接收改过的任务描述并写入数据表
			$data = EntryCooperation::find($id);
            $crew = $data->crews()->pluck('user_id')->toArray();
            $manage_id = $data->getEntry->manage_id;
            array_push($crew, $manage_id);
            //标题、内容不能为空
            if($title && $content && in_array($user, $crew)){
            	// 记录投票
                $result = EntryCooperationVote::cooperationVote($id,$data->eid,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=EntryCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==2){
        	$data = ArticleCooperation::find($id);
        	$crew = $data->crews()->pluck('user_id')->toArray();
            $manage_id = $data->getArticle->manage_id;
            array_push($crew, $manage_id);
            //标题、内容不能为空
            if($title && $content && in_array($user, $crew)){
            	// 记录投票
                $result = ArticleCooperationVote::cooperationVote($id,$data->aid,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=ArticleCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==3){
            $data = ExamCooperation::find($id);
            $crew = $data->crews()->pluck('user_id')->toArray();
            $manage_id = $data->getExam->manage_id;
            array_push($crew, $manage_id);
            //标题、内容不能为空
            if($title && $content && in_array($user, $crew)){
                // 记录投票
                $result = ExamCooperationVote::cooperationVote($id,$data->exam_id,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=ExamCooperationVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }elseif($scope==4){
            $data = Group::find($id);
            $crew = $data->members()->pluck('user_id')->toArray();
            array_push($crew, $data->manage_id);
            $deadline = Carbon::now()->addDays(3);
            if($title && $content && in_array($user, $crew)){
                $result = GroupVote::groupVote($id,$type,$deadline,$user,$username,$title,$content);
            }
            if($result)$votes=GroupVote::where([['cooperation_id',$id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
        }

        return ['success'=>$result? true:false,'votes'=>$votes];
    }


	//退出小组操作
    public function teamQuit(Request $request){
    	$scope = $request->scope;
    	$result = false;
		$cooperation_id = $request->cooperation_id;
        $crews = '';
        $crewArr = '';
		if($scope==1){
			$result = EntryCooperationUser::cooperationMemberQuit($cooperation_id,auth('api')->user()->id);
            if($result){
                $cooperation = EntryCooperation::find($cooperation_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Entry::find($cooperation->eid)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
		}elseif($scope==2){
			$result = ArticleCooperationUser::cooperationMemberQuit($cooperation_id,auth('api')->user()->id);
            if($result){
                $cooperation = ArticleCooperation::find($cooperation_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Article::find($cooperation->aid)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
		}elseif($scope==3){
            $result = ExamCooperationUser::cooperationMemberQuit($cooperation_id,auth('api')->user()->id);
            if($result){
                $cooperation = ExamCooperation::find($cooperation_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Exam::find($cooperation->exam_id)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
        }elseif($scope==4){
            $gid = $request->input('gid');
            $result = GroupUser::groupMemberQuit($cooperation_id,auth('api')->user()->id);
            if($result){
                $cooperation = Group::find($cooperation_id);
                $crewArr = $cooperation->members()->pluck('user_id')->toArray();
                array_push($crewArr,$cooperation->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
        }
		return ['success'=>$result? true:false,'crews'=>$crews,'crewArr'=>$crewArr];
    }

    //协作计划小组内弹劾组长投票********************************************************************
    public function leaderImpeach(Request $request,$id){
    	$id = $request->cooperation_id;
        $data = EntryCooperation::find($id);
        // dd($id,$encid);
        if(count($data) && $request->isMethod('post')){
            //接收改过的任务描述并写入数据表
            $crew = $data->crews()->pluck('user_id')->toArray();
            array_push($crew, $data->initiate_id);
            $user = auth('api')->user()->id;
            $username = auth('api')->user()->username;
            $title = $request->input('title');
            $content = $request->input('vote_content');
            $type = '3';
            $deadline = Carbon::now()->addDays(3);
            //标题、内容不能为空
            if($title && $content && in_array($user, $crew) && !EntryCooperationVote::where([['cooperation_id',$id],['type','3'],['status','0']])->exists()){
            	// 记录投票
                $result = EntryCooperationVote::cooperationVote($id,$data->eid,$type,$deadline,$user,$username,$title,$content);
                return $result;
            }
        }else{  
            return view('home/encyclopedia/cooperation/leaderImpeach',compact('id'));
        }
    }

    //处理协作过程中的投票
    public function voteStandPoint(Request $request){
    	$id = $request->vote_id;
    	$scope = $request->scope;
    	$userId = auth('api')->user()->id;
   		$username = auth('api')->user()->username;
   		$point = $request->standpoint;
       	$createtime = Carbon::now();
        $votes = '';
        $history_votes = '';
        $crewArr = '';
        $crews = '';
       	if($scope==1){
       		// 此id是投票id
	    	$vote=EntryCooperationVote::find($id);
	        if($id && $vote->exists()){
	           	// 判断用户是否具有投票权限以及是否在本投票中已经投过票
	           	$cooperation = EntryCooperation::find($vote->cooperation_id);
	           	$crew = $cooperation->crews()->pluck('user_id')->toArray();
                $manage_id = Entry::find($cooperation->eid)->manage_id;
	           	array_push($crew, $manage_id);
	           	$record = EntryCooperationVoteRecord::where('vote_id',$id)->pluck('user_id')->toArray();
                if(Carbon::now()>=$vote->deadline && $vote->status=='0'){
                    $result = EntryCooperationVote::where('id',$id)->update([
                            'status'=>'2',
                            'remark'=>'本次投票由于过期关闭投票通道。'
                        ]);
                }
	       		if(in_array($userId, $crew) && !in_array($userId, $record) && Carbon::now() < $vote->deadline && $vote->status == '0'){
	                $result = EntryCooperationVoteRecord::voteAdd($id,$userId,$username,$point,$createtime);
	            }
                if($result){
                    $votes=EntryCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
                    $history_votes=EntryCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
                    if($vote->type===2){
                        // 2涉及到crew变动
                        $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                        array_push($crewArr,Entry::find($cooperation->eid)->manage_id);
                        $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
                    }
                }
	        }

       	}elseif($scope==2){
       		$vote=ArticleCooperationVote::find($id);
       		if($id && $vote->exists()){
	           	$cooperation = ArticleCooperation::find($vote->cooperation_id);
	           	$crew = $cooperation->crews()->pluck('user_id')->toArray();
                $manage_id = Article::find($cooperation->aid)->manage_id;
	           	array_push($crew, $manage_id);
	           	$record = ArticleCooperationVoteRecord::where('vote_id',$id)->pluck('user_id')->toArray();
                if(Carbon::now()>=$vote->deadline && $vote->status=='0'){
                    $result = ArticleCooperationVote::where('id',$id)->update([
                            'status'=>'2',
                            'remark'=>'本次投票由于过期关闭投票通道。'
                        ]);
                }
	       		if(in_array($userId, $crew) && !in_array($userId, $record) && Carbon::now() < $vote->deadline && $vote->status == '0'){
	                $result = ArticleCooperationVoteRecord::voteAdd($id,$userId,$username,$point,$createtime);
	            }
                if($result){
                    $votes=ArticleCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
                    $history_votes=ArticleCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
                    if($vote->type===2){
                        // 2涉及到crew变动
                        $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                        array_push($crewArr,Article::find($cooperation->aid)->manage_id);
                        $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
                    }
                }
        	}
       	}elseif($scope==3){
            $vote=ExamCooperationVote::find($id);
            if($id && $vote->exists()){
                $cooperation = ExamCooperation::find($vote->cooperation_id);
                $crew = $cooperation->crews()->pluck('user_id')->toArray();
                $manage_id = Exam::find($cooperation->exam_id)->manage_id;
                array_push($crew, $manage_id);
                $record = ExamCooperationVoteRecord::where('vote_id',$id)->pluck('user_id')->toArray();
                if(Carbon::now()>=$vote->deadline && $vote->status=='0'){
                    $result = ExamCooperationVote::where('id',$id)->update([
                            'status'=>'2',
                            'remark'=>'本次投票由于过期关闭投票通道。'
                        ]);
                }
                if(in_array($userId, $crew) && !in_array($userId, $record) && Carbon::now() < $vote->deadline && $vote->status == '0'){
                    $result = ExamCooperationVoteRecord::voteAdd($id,$userId,$username,$point,$createtime);
                }
                if($result){
                    $votes=ExamCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->limit(15)->get();
                    $history_votes=ExamCooperationVote::where([['cooperation_id',$vote->cooperation_id],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
                    if($vote->type===2){
                        // 2涉及到crew变动
                        $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                        array_push($crewArr,Exam::find($cooperation->exam_id)->manage_id);
                        $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
                    }
                }
            }
        }elseif($scope==4){
            $vote=GroupVote::find($id);
            if($id && $vote->exists()){
                // 判断用户是否具有投票权限以及是否在本投票中已经投过票
                $group = Group::find($vote->gid);
                $crew = $group->members()->pluck('user_id')->toArray();
                array_push($crew, $group->manage_id);
                $record = GroupVoteRecord::where('vote_id',$id)->pluck('user_id')->toArray();
                if(Carbon::now()>=$vote->deadline && $vote->status=='0'){
                    $result = GroupVote::where('id',$id)->update([
                            'status'=>'2',
                            'remark'=>'本次投票由于过期关闭投票通道。'
                        ]);
                }
                if(in_array($userId, $crew) && !in_array($userId, $record) && Carbon::now() < $vote->deadline && $vote->status == '0'){
                    $result = GroupVoteRecord::voteAdd($id,$userId,$username,$point,$createtime);               
                }
            }
        }
    	return ['success'=>$result? true:false,'crews'=>$crews,'crewArr'=>$crewArr,'votes'=>$votes,'history_votes'=>$history_votes];	
    }
}
