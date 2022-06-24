<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use DB;

class CooperationLeaderController extends Controller
{
    //清退组员权限
    public function memberFired(Request $request){
    	$crew_id = $request->crew_id;
    	$cooperation_id = $request->cooperation_id;
    	$scope = $request->scope;
    	$result = false;
    	$user_id = auth('api')->user()->id;
        $crews = '';
        $crewArr = '';
    	if($scope==1){
    		$data = EntryCooperation::find($cooperation_id);
            $manage_id = Entry::find($data->eid)->manage_id;
	    	$crew = $data->crews()->pluck('user_id')->toArray();
	    	if(in_array($crew_id, $crew) && $user_id == $manage_id){
	    		$result = EntryCooperationUser::cooperationMemberFire($cooperation_id,$crew_id);
	    	}
            if($result){
                $crewArr = $data->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Entry::find($data->eid)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
    	}elseif($scope==2){
    		$data = ArticleCooperation::find($cooperation_id);
            $manage_id = Article::find($data->aid)->manage_id;
	    	$crew = $data->crews()->pluck('user_id')->toArray();
	    	if(in_array($crew_id, $crew) && $user_id == $manage_id){
	    		$result = ArticleCooperationUser::cooperationMemberFire($cooperation_id,$crew_id);
	    	}
            if($result){
                $crewArr = $data->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Article::find($data->aid)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
    	}elseif($scope==3){
            $data = ExamCooperation::find($cooperation_id);
            $manage_id = Exam::find($data->exam_id)->manage_id;
            $crew = $data->crews()->pluck('user_id')->toArray();
            if(in_array($crew_id, $crew) && $user_id == $manage_id){
                $result = ExamCooperationUser::cooperationMemberFire($cooperation_id,$crew_id);
            }
            if($result){
                $crewArr = $data->crews()->pluck('user_id')->toArray();
                array_push($crewArr,Exam::find($data->exam_id)->manage_id);
                $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
            }
        }elseif($scope==4){
            $data = Group::find($cooperation_id);
            $member = $data->members()->pluck('user_id')->toArray();
            if(in_array($crew_id, $member) && $user_id == $data->manage_id){
                $result = GroupUser::groupMemberFire($cooperation_id,$crew_id);
            }
        }
    	
    	return ['success'=>$result? true:false,'crews'=>$crews,'crewArr'=>$crewArr];
    }

    //放弃自管理
    public function manageQuit(Request $request){
        $data= $request->data;
        $scope = $data['scope'];
        $content_id = $data['target_id'];
        $cooperation_id = $data['cooperation_id'];
        $user = auth('api')->user();
        $result = false;
        $user_id = $user->id;
        $status = 3;
        if($scope==1){
            $data = EntryCooperation::find($cooperation_id);
            $manage_id = Entry::find($data->eid)->manage_id;
            
            if($content_id==$data->eid && $user_id == $manage_id){
                $result = Entry::where('id',$content_id)->update(['manage_id'=>0,'cooperation_id'=>0]);
                EntryCooperation::cooperationShutDown($cooperation_id,$status);
            }
        }elseif($scope==2){
            $data = ArticleCooperation::find($cooperation_id);
            $manage_id = Article::find($data->aid)->manage_id;
            if($content_id==$data->aid && $user_id == $manage_id){
                $result = Article::where('id',$content_id)->update(['manage_id'=>0,'cooperation_id'=>0]);
                ArticleCooperation::cooperationShutDown($cooperation_id,$status);
            }
        }elseif($scope==3){
            $data = ExamCooperation::find($cooperation_id);
            $manage_id = Exam::find($data->exam_id)->manage_id;
            if($content_id==$data->exam_id && $user_id == $manage_id){
                $result = Exam::where('id',$content_id)->update(['manage_id'=>0,'cooperation_id'=>0]);
                ExamCooperation::cooperationShutDown($cooperation_id,$status);
            }
        }elseif($scope==4){
            $data = Group::find($cooperation_id);
            if($user_id == $data->manage_id){
                $result = Group::where('id',$content_id)->update(['manage_id'=>0]);
            }
        }
        
        return ['success'=>$result? true:false];
    }

    // 成为自管理员
    public function beContentManager(Request $request) {
        $data= $request->data;
        $scope = $data['scope'];
        $content_id = $data['content_id'];
        $userId = $data['user_id'];
        $user = auth('api')->user();
        $result = false;
        $user_id = $user->id;
        if($scope==1){
            $content = Entry::find($content_id);
            if($content_id && $content->manage_id==0 && $user_id==$userId && $user->update(['gold'=>($user->gold-$content->level)])){
                $result = Entry::managerUpdate($content_id,$user_id);
            }
        }elseif($scope==2){
            $content = Article::find($content_id);
            if($content_id && $content->manage_id==0 && $user_id==$userId && $user->update(['gold'=>($user->gold-$content->level)])){
                $result = Article::manageUpdate($content_id,$user_id);
            }
        }elseif($scope==3){
            $content = Exam::find($content_id);
            if($content_id && $content->manage_id==0 && $user_id==$userId && $user->update(['gold'=>($user->gold-$content->level)])){
                $result = Exam::manageUpdate($content_id,$user_id);
            }
        }elseif($scope==4){
            $content = Group::find($content_id);
            if($content_id && $content->manage_id==0 && $user_id==$userId && $user->update(['gold'=>($user->gold-$content->level)])){
                $result = Group::manageUpdate($content_id,$user_id);
            }
        }
        
        return ['success'=>$result? true:false];
    }

    //授予组长权限,只在该协作计划内有效，不会清空自管理员权限，这个功能暂时不做
    public function leaderAwarded(Request $request,$crew_id,$cooperation_id){
    	return ['此功能暂时不做'];
    }
}
