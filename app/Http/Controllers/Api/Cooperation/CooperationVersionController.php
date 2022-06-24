<?php

namespace App\Http\Controllers\Api\Cooperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Home\Cooperation\EntryContributeValue;
use App\Models\Home\Cooperation\ArticleContributeValue;
use App\Models\Home\Cooperation\ExamContributeValue;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;


class CooperationVersionController extends Controller
{
    //
    public function versionUpdate(Request $request) {
        // return $request;
    	$data = $request->data;
        // return $data;
        $scope = $data['scope'];
    	$nullCooperation = $data['nullCooperation'];
        $c_id = $data['cooperation_id'];
    	$user = auth('api')->user();
        $new = '';
        $cont = '';
    	$events = '';
    	// return $scope;
    	if($scope==1){
            
            if($data['content_id'] && $user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
                $timelimit = $data['deadline'];
                $deadline = Carbon::now()->addMonths($timelimit);
                $cooperation = $c_id?EntryCooperation::find($c_id):'';
                $new = EntryCooperation::entryCooperationCreate($data['content_id'],$data['cid'],$data['cooperation_title'],$data['target_level'],$timelimit,$deadline,$data['seeking'],$data['newAssign'],$cooperation?$cooperation->version+1:1,$user->id,$user->username);
                $result = $new->id;
                Entry::where('id',$data['content_id'])->update(['cooperation_id'=>$result]);
                // 这里要加入自管理员
                EntryContributeValue::contributeAdd($result,$user->id,0);
                // 如果是版本升级 需要变更原计划
                if(!$nullCooperation && $c_id) {
                    $status = ($data['targetDone']?1:2);
                    EntryCooperation::where('id',$c_id)->update(['status'=>$status]);
                    if($data['inherit']==1){
                        $crews = $cooperation->crews()->pluck('user_id')->toArray();
                        if($crews){
                            foreach($crews as $crew) {
                                EntryContributeValue::contributeAdd($result,$crew,0);
                                EntryCooperationUser::cooperationMemberJoin($result,$crew,Carbon::now());
                            }
                        }
                    }
                }
                $events = EntryCooperationEvent::where('cooperation_id',$result)->orderBy('created_at','desc')->limit(20)->get();
            }
        } elseif($scope==2){
            
            if($data['content_id'] && $user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
                $timelimit = $data['deadline'];
                $deadline = Carbon::now()->addMonths($timelimit);
                $cooperation = $c_id?ArticleCooperation::find($c_id):'';
                $new = ArticleCooperation::articleCooperationCreate($data['content_id'],$data['cid'],$data['cooperation_title'],$data['target_level'],$data['secret'],$timelimit,$deadline,$data['seeking'],$data['newAssign'],$cooperation?$cooperation->version+1:1,$user->id,$user->username);
                $result = $new->id;
                Article::where('id',$data['content_id'])->update(['cooperation_id'=>$result]);
                ArticleContributeValue::contributeAdd($result,$user->id,0);
                if(!$nullCooperation && $c_id) {
                    $status = ($data['targetDone']?1:2);
                    ArticleCooperation::where('id',$c_id)->update(['status'=>$status]);
                    if($data['inherit']==1){
                        $crews = $cooperation->crews()->pluck('user_id')->toArray();
                        if($crews){
                            foreach($crews as $crew) {
                                ArticleContributeValue::contributeAdd($result,$crew,0);
                                ArticleCooperationUser::cooperationMemberJoin($result,$crew,Carbon::now());
                            }
                        }
                    } 
                }
                $events = ArticleCooperationEvent::where('cooperation_id',$result)->orderBy('created_at','desc')->limit(20)->get();
            }
        }elseif($scope==3){
            
            if($data['content_id'] && $user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
                $timelimit = $data['deadline'];
                $deadline = Carbon::now()->addMonths($timelimit);
                $cooperation = $c_id?ExamCooperation::find($c_id):'';
                $new = ExamCooperation::examCooperationCreate($data['content_id'],$data['cid'],$data['cooperation_title'],$data['target_level'],$timelimit,$deadline,$data['seeking'],$data['newAssign'],$cooperation?$cooperation->version+1:1,$user->id,$user->username);
                $result = $new->id;
                Exam::where('id',$data['content_id'])->update(['cooperation_id'=>$result]);
                ExamContributeValue::contributeAdd($result,$user->id);
                if(!$nullCooperation && $c_id) {
                    $status = ($data['targetDone']?1:2);
                    ExamCooperation::where('id',$c_id)->update(['status'=>$status]);
                    if($data['inherit']==1){
                        $crews = $cooperation->crews()->pluck('user_id')->toArray();
                        if($crews){
                            foreach($crews as $crew) {
                                ExamContributeValue::contributeAdd($result,$crew,0);
                                ExamCooperationUser::cooperationMemberJoin($result,$crew,Carbon::now());
                            }
                        }
                    }
                }
                $events = ExamCooperationEvent::where('cooperation_id',$result)->orderBy('created_at','desc')->limit(20)->get();
            }
        }
    	return ['success'=>$new?true:false];
    }

    // 内容的降级
    public function levelLower(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$user = auth('api')->user();
    	$result = false;
    	if($scope==1) {
    		$target = Entry::find($data['target_id']);
    		if($target->level==3) {
    			$result = Entry::where('id',$target->id)->update(['level'=>$target->level-1]);
    		}
    	}elseif($scope==2) {
    		$target = Article::find($data['target_id']);
    		if($target->level==5) {
    			$result = Article::where('id',$target->id)->update(['level'=>$target->level-1]);
    		}
    	}elseif($scope==3) {
    		$target = Exam::find($data['target_id']);
    		if($target->level==5) {
    			$result = Exam::where('id',$target->id)->update(['level'=>$target->level-1]);
    		}
    	}
    	return ['success'=>$result?true:false];
    }

    public function simpleUpgrade(Request $request) {
        $data = $request->data;
        $scope = $data['scope'];
        $level = $data['level'];
        $user = auth('api')->user();
        $result = false;
        // return $request;
        if($scope==1) {
            $target = Entry::find($data['target_id']);
            if($target->level==$level&&$level==1) {
                $content = $target->entryContents->count();
                $cooperation = $target->entryCooperation->exists();
                $keyword = $target->keywords->count();
                // 1级升级二级，判断标准就是有完整的框架
                if($target->summary && $content && $cooperation && $keyword){
                    $result = Entry::where('id',$target->id)->update(['level'=>2]);
                } 
            }else if($target->level==$level&&$level==2) {
                $content = $target->entryContents->count();
                $cooperation = $target->entryCooperation->exists();
                $reference = $target->entryReference->count();
                $keyword = $target->keywords->count();
                $extends = $target->extendedEntryReadings->count()+$target->extendedArticleReadings->count()+$target->extendedExamReadings->count();
                // 2升3，需要完整的框架、文献、延伸阅读
                if($target->summary && $content>2 && $cooperation && $reference && $keyword && $extends>4){
                    $result = Entry::where('id',$target->id)->update(['level'=>3]);
                } 
            }
        }elseif($scope==2) {
            $target = Article::find($data['target_id']);
            if($target->level==$level&&$level==1) {
                $content = $target->getArticleContents->count();
                $cooperation = $target->cooperation->exists();
                $keyword = $target->keywords->count();
                if($target->summary && $content && $cooperation && $keyword){
                    $result = Article::where('id',$target->id)->update(['level'=>2]);
                } 
            }else if($target->level==$level&&$level==2) {
                $content = $target->getArticleContents->count();
                $cooperation = $target->cooperation->exists();
                $reference = $target->references->count();
                $keyword = $target->keywords->count();
                $extends = $target->extendedEntryReadings->count()+$target->extendedArticleReadings->count()+$target->extendedExamReadings->count();
                if($target->summary && $content>2 && $cooperation && $reference && $keyword && $extends>4){
                    $result = Article::where('id',$target->id)->update(['level'=>3]);
                } 
            }
        }elseif($scope==3) {
            $target = Exam::find($data['target_id']);
            if($target->level==$level&&$level==1) {
                // 试卷的标准不同******************************************************************************
                $content = $target->getArticleContents->count();
                $cooperation = $target->entryCooperation->exists();
                $keyword = $target->keywords->count();
                if($target->summary && $content && $cooperation && $keyword){
                    $result = Article::where('id',$target->id)->update(['level'=>2]);
                } 
            }else if($target->level==$level&&$level==2) {
                $content = $target->getArticleContents->count();
                $cooperation = $target->cooperation->exists();
                $reference = $target->entryReference->count();
                $keyword = $target->keywords->count();
                if($target->summary && $content>2 && $cooperation && $reference && $keyword){
                    $result = Article::where('id',$target->id)->update(['level'=>3]);
                } 
            }
        }
        return ['success'=>$result?true:false];
    }
}
