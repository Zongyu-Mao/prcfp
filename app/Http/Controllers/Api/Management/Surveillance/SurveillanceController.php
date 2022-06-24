<?php

namespace App\Http\Controllers\Api\Management\Surveillance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Models\Committee\Surveillance\SurveillanceRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleRecord;
use App\Models\Committee\Surveillance\SurveillanceExamRecord;
use App\Models\Committee\Surveillance\SurveillanceMark;
use App\Models\Committee\Surveillance\SurveillanceArticleMark;
use App\Models\Committee\Surveillance\SurveillanceExamMark;
use App\Models\Committee\Surveillance\SurveillanceMarkType;
use App\Models\Committee\Surveillance\SurveillanceMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceExamMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceMarkDisposeWay;
use App\Models\Committee\Surveillance\SurveillanceWarning;
use App\Models\Committee\Surveillance\SurveillanceArticleWarning;
use App\Models\Committee\Surveillance\SurveillanceExamWarning;
use App\Models\Committee\Surveillance\GroupMark;
use App\Models\Committee\Surveillance\GroupWarning;
use App\Models\Committee\Surveillance\GroupMarkRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SurveillanceController extends Controller
{
    //
    public function surveillance(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$result = 0;
        $user = auth('api')->user();
        $stand = $data['stand'];
        $status = ($stand==1?2:0); // status==2时，代表已经通过

    	// 各种限制后面再来，提交的前提是有问题的需要改进
    	if($scope==1) $basic = Entry::find($data['id'])->only('surveillance','level');
    	if($scope==2) $basic = Article::find($data['id'])->only('surveillance','level');
    	if($scope==3) $basic = Exam::find($data['id'])->only('surveillance','level');
        $s = ($stand==1?$basic['level']:$basic['level']*2);
        $bsc = '';
        $bc = '';
    	if($basic['surveillance']===0) {
            // || ($basic['surveillance']==1 && $basic['level']==2)
    		// 这时是可以提交巡查结果的，目前仅需要创建巡查
    		if($scope==1 && Entry::surveillance($data['id'],$s)){
                $result = SurveillanceRecord::newRecord($user->id,$data['id'],$status,$stand,$user->id,$data['content']);
                if($result){
                    $bsc = Entry::where('id',$data['id'])->with('entryAvatar')->first();//basic重复了
                    $bc = SurveillanceRecord::where('sid',$data['id'])->exists()?SurveillanceRecord::where('sid',$data['id'])->get():'';
                }
            }
            if($scope==2 && Article::surveillance($data['id'],$s)){
                $result = SurveillanceArticleRecord::newRecord($user->id,$data['id'],$status,$stand,$user->id,$data['content']);
                if($result){
                    $bsc = Article::where('id',$data['id'])->with('articleAvatar')->first();//basic重复了
                    $bc = SurveillanceArticleRecord::where('sid',$data['id'])->exists()?SurveillanceArticleRecord::where('sid',$data['id'])->get():'';
                }
            }
            if($scope==3 && Exam::surveillance($data['id'],$s)){
                $result = SurveillanceExamRecord::newRecord($user->id,$data['id'],$status,$stand,$user->id,$data['content']);
                if($result){
                    $bsc = Exam::find($data['id'])->first();//basic重复了
                    $bc = SurveillanceExamRecord::where('sid',$data['id'])->exists()?SurveillanceExamRecord::where('sid',$data['id'])->get():'';
                }
            }
    	}
    	return [
            'success' => $result?true:false,
            'basic' => $bsc,
    		'backContent' => $bc,
    	];
    }

    // 自管理员请求巡查
    public function surveillanceRequest(Request $request) {
        // 由于是请求巡查，因此stand和status都是0
        $data = $request->data;
        $scope = $data['scope'];
        $target_id = $data['target_id'];
        $cooperation_id = $data['cooperation_id'];
        $result = false;
        $user = auth('api')->user();
        $c = 'surveillance request';
        $s=1;//这里更改主内容surveillance为1，代表已经申请过但是还没有结果
        $stand = 0;
        $status = ($stand==1?2:0); // status==2时，代表已经通过
        // 各种限制后面再来，提交的前提是有问题的需要改进
        if($scope==1) $basic = Entry::find($target_id)->only('surveillance','level');
        if($scope==2) $basic = Article::find($target_id)->only('surveillance','level');
        if($scope==3) $basic = Exam::find($target_id)->only('surveillance','level');
        if($basic['surveillance']==0 && $basic['level']==1) {
            // 这时是可以提交巡查结果的
            if($scope==1 && Entry::surveillance($target_id,$s))$result = SurveillanceRecord::newRecord($user->id,$target_id,$status,$stand,$user->id,$c);
            if($scope==2 && Article::surveillance($target_id,$s))$result = SurveillanceArticleRecord::newRecord($user->id,$target_id,$status,$stand,$user->id,$c);
            if($scope==3 && Exam::surveillance($target_id,$s))$result = SurveillanceExamRecord::newRecord($user->id,$target_id,$status,$stand,$user->id,$c);
        }
        // 这里不返回basic，直接在前端更改s的状态即可
        return [
            'success' => $result?true:false
        ];
    }

    public function markWarning(Request $request) {
    	$data = $request->data;
    	$type = $data['type'];
        $scope = $data['scope'];
    	$weight = $data['weight'];
        $result = 0;
    	$status = 0;
    	if($type==2) {
    		// 标记
            $marks = '';
    		foreach($data['types'] as $t) {
    			if( $t!=end($data['types'] )){ $marks.=$t.';'; } else { $marks.=$t; }
    		}
            if($scope==1){
                $result = SurveillanceMark::newMark(auth('api')->user()->id,$weight,$data['tcid'],$data['id'],$marks,$data['disposeWay'],0,$data['content']);
                if($result) {
                    $bsc = Entry::where('id',$data['id'])->with('entryAvatar')->first();
                    $bc = SurveillanceMark::where('sid',$data['id'])->exists()?SurveillanceMark::where('sid',$data['id'])->get():'';//marks
                }
            }else if($scope==2){
                $result = SurveillanceArticleMark::newMark(auth('api')->user()->id,$weight,$data['tcid'],$data['id'],$marks,$data['disposeWay'],0,$data['content']);
                if($result) {
                    $bsc = Article::where('id',$data['id'])->with('articleAvatar')->first();
                    $bc = SurveillanceArticleMark::where('sid',$data['id'])->exists()?SurveillanceArticleMark::where('sid',$data['id'])->get():'';//marks
                }
            }else if($scope==3){
                $result = SurveillanceExamMark::newMark(auth('api')->user()->id,$weight,$data['tcid'],$data['id'],$marks,$data['disposeWay'],0,$data['content']);
                if($result) {
                    $bsc = Exam::find($data['id']);
                    $bc = SurveillanceExamMark::where('sid',$data['id'])->exists()?SurveillanceExamMark::where('sid',$data['id'])->get():'';//marks
                }
            }else if($scope==4){
                $result = GroupMark::newMark(auth('api')->user()->id,$weight,$data['tcid'],$data['id'],$marks,$data['disposeWay'],0,$data['content']);
                if($result) {
                    $bsc = Group::find($data['id']);
                    $bc = GroupMark::where('sid',$data['id'])->get()??[];//marks
                }
            }
    	}else if ($type==3) {
            // warning
            if($scope==1){
                $result = SurveillanceWarning::newWarning(auth('api')->user()->id,$data['id'],$data['content'],$status,Carbon::now());
                if($result) {
                    $bsc = Entry::where('id',$data['id'])->with('entryAvatar')->first();
                    $bc = SurveillanceWarning::where('sid',$data['id'])->exists()?SurveillanceWarning::where('sid',$data['id'])->get():'';
                }
            }else if($scope==2){
                $result = SurveillanceArticleWarning::newWarning(auth('api')->user()->id,$data['id'],$data['content'],$status,Carbon::now());
                if($result) {
                    $bsc = Article::where('id',$data['id'])->with('articleAvatar')->first();
                    $bc = SurveillanceArticleWarning::where('sid',$data['id'])->exists()?SurveillanceArticleWarning::where('sid',$data['id'])->get():'';
                }
            }else if($scope==3){
                $result = SurveillanceExamWarning::newWarning(auth('api')->user()->id,$data['id'],$data['content'],$status,Carbon::now());
                if($result) {
                    $bsc = Exam::find($data['id']);
                    $bc = SurveillanceExamWarning::where('sid',$data['id'])->exists()?SurveillanceExamWarning::where('sid',$data['id'])->get():'';
                }
            }else if($scope==4){
                $result = GroupWarning::newWarning(auth('api')->user()->id,$data['id'],$data['content'],$status,Carbon::now());
                if($result) {
                    $bsc = Group::find($data['id']);
                    $bc = GroupWarning::where('sid',$data['id'])->exists()?GroupWarning::where('sid',$data['id'])->get():'';
                }
            }
        }
    	return [
            'success' => $result?true:false,
            'basic' => $bsc,
            'backContent' => $bc,
        ];
    }

    public function passSurveillance(Request $request) {
        $data = $request->data;
        $id = $data['id'];
        $type = $data['type'];
        $scope = $data['scope'];
        $result = 0;
        $bc = '';
        $bsc = '';
        $status = ($type===1?1:2);//type为1是请求，2是通过，此时record已经建立，因此其实与basic没有关系
        // 正在请求巡查通过
        if($scope==1){
            $s = SurveillanceRecord::find($id);
            if($s->status==0 && $s->stand==2)$result = SurveillanceRecord::recordUpdate($id,$status,2);//在申请时，stand可以是任何数字，这里取2
            if($s->status==1 && $s->stand==2)$result = SurveillanceRecord::recordUpdate($id,$status,1);
            if($result){
                $bsc = Entry::where('id',$s->sid)->with('entryAvatar')->first();//basic重复了
                $bc = SurveillanceRecord::where('sid',$s->sid)->exists()?SurveillanceRecord::where('sid',$s->sid)->get():'';
            }
        } else if($scope==2) {
            $s = SurveillanceArticleRecord::find($id);
            if($s->status==0 && $s->stand==2)$result = SurveillanceArticleRecord::recordUpdate($id,$status,2);
            if($s->status==1 && $s->stand==2)$result = SurveillanceArticleRecord::recordUpdate($id,$status,1);
            if($result) {
                $bsc = Article::where('id',$s->sid)->with('articleAvatar')->first();
                $bc = SurveillanceArticleRecord::where('sid',$s->sid)->exists()?SurveillanceArticleRecord::where('sid',$s->sid)->get():'';//marks
            }
        } else if($scope==3) {
             $s = SurveillanceExamRecord::find($id);
            if($s->status==0 && $s->stand==2)$result = SurveillanceExamRecord::recordUpdate($id,$status,2);
            if($s->status==1 && $s->stand==2)$result = SurveillanceExamRecord::recordUpdate($id,$status,1);
            if($result) {
                $bsc = Exam::find($s->sid);
                $bc = SurveillanceExamRecord::where('sid',$s->sid)->exists()?SurveillanceExamRecord::where('sid',$s->sid)->get():'';
            }
        }
        return [
            'success' => $result?true:false,
            'basic' => $bsc,//考虑不要
            'backContent' => $bc,
        ];
    }

    public function repealWarning(Request $request) {
        $data = $request->data;
        $id = $data['id'];
        $type = $data['type'];
        $scope = $data['scope'];
        $result = 0;
        $bc = '';//对警示和标记，暂时不涉及basicContent，因此这里先不初始basic
        $status = ($type===3?1:2);//type为3是请求，4是通过
            // 请求撤销警示
            if($scope==1){
                $s = SurveillanceWarning::find($id);
                if(($type===3&&$s->status==0) || ($type!=3&&$s->status==1)){
                    $result = SurveillanceWarning::warnUpdate($id,$status);
                    if($result) {
                        $bc = SurveillanceWarning::where('sid',$s->sid)->exists()?SurveillanceWarning::where('sid',$s->sid)->get():'';
                    }
                }
            } else if($scope==2) {
                 $s = SurveillanceArticleWarning::find($id);
                if(($type===3&&$s->status==0) || ($type!=3&&$s->status==1)){
                    $result = SurveillanceArticleWarning::warnUpdate($id,$status);
                    if($result) {
                        $bc = SurveillanceArticleWarning::where('sid',$s->sid)->exists()?SurveillanceArticleWarning::where('sid',$s->sid)->get():'';
                    }
                }
            } else if($scope==3) {
                $s = SurveillanceExamWarning::find($id);
                if(($type===3&&$s->status==0) || ($type!=3&&$s->status==1)){
                    $result = SurveillanceExamWarning::warnUpdate($id,$status);
                    if($result) {
                        $bc = SurveillanceExamWarning::where('sid',$s->sid)->exists()?SurveillanceExamWarning::where('sid',$s->sid)->get():'';
                    }
                }
            } else if($scope==4) {
                $s = GroupWarning::find($id);
                if(($type===3&&$s->status==0) || ($type!=3&&$s->status==1)){
                    $result = GroupWarning::warnUpdate($id,$status);
                    if($result) {
                        $bc = GroupWarning::where('sid',$s->sid)->get()??[];
                    }
                }
            }
                      
        return [
            'success' => $result?true:false,
            'backContent' => $bc,
        ];
    }
}
