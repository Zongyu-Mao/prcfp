<?php

namespace App\Http\Controllers\Api\Management\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Committee\Surveillance\SurveillanceRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleRecord;
use App\Models\Committee\Surveillance\SurveillanceExamRecord;
use App\Models\Committee\Surveillance\SurveillanceMark;
use App\Models\Committee\Surveillance\SurveillanceMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleMark;
use App\Models\Committee\Surveillance\SurveillanceArticleMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceExamMark;
use App\Models\Committee\Surveillance\SurveillanceExamMarkReactRecord;
use App\Models\Committee\Surveillance\SurveillanceWarning;
use App\Models\Committee\Surveillance\SurveillanceArticleWarning;
use App\Models\Committee\Surveillance\SurveillanceExamWarning;
use App\Models\Committee\Surveillance\GroupMark;
use App\Models\Committee\Surveillance\GroupMarkRecord;
use App\Models\Committee\Surveillance\GroupWarning;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommitteeSurveillanceController extends Controller
{
    //
    public function committeeSurveillances(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$pageSize = $data['pageSize'];
    	if($scope==1)$surveillances = SurveillanceRecord::orderBy('created_at','desc')->with('author')->with('content')->paginate($pageSize);
        if($scope==2)$surveillances = SurveillanceArticleRecord::orderBy('created_at','desc')->with('author')->with('content')->paginate($pageSize);
        if($scope==3)$surveillances = SurveillanceExamRecord::orderBy('created_at','desc')->with('author')->with('content')->paginate($pageSize);
        return $surveillances = [
            'surveillances' => $surveillances
        ];
    }

    public function markDetail(Request $request) {
        $data = $request->data;
        // 这是前段回来的三个参数
        $scope = $data['scope'];
        $id = $data['id'];
        $tcid = $data['tcid'];
        $mark = '';
        $rs = '';
        if($scope==1){
            $mark = SurveillanceMark::where('id',$id)->with('author')->with('content')->with('dispose')->first();
            $rs = SurveillanceMarkReactRecord::where('mark_id',$id)->with('getOperator')->get();
        }
        if($scope==2){
            $mark = SurveillanceArticleMark::where('id',$id)->with('author')->with('content')->with('dispose')->with('records')->first();
            $rs = SurveillanceArticleMarkReactRecord::where('mark_id',$id)->with('getOperator')->get();
        }
        if($scope==3){
            $mark = SurveillanceExamMark::where('id',$id)->with('author')->with('content')->with('dispose')->with('records')->first();
            $rs = SurveillanceExamMarkReactRecord::where('mark_id',$id)->with('getOperator')->get();
        }
        if($scope==4){
            $mark = GroupMark::where('id',$id)->with('author')->with('content')->with('dispose')->with('records')->first();
            $rs = GroupMarkRecord::where('mark_id',$id)->with('getOperator')->get();
        }
        $comm = $mark->author->getCommittee;
        return $mark = [
            'mark' => $mark,
            'comm' => $comm,
            'records'=>$rs
        ];
    }

    public function committeeSurveillanceMarks(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$pageSize = $data['pageSize'];
    	if($scope==1)$marks = SurveillanceMark::orderBy('created_at','desc')->with('author')->with('content')->with('dispose')->paginate($pageSize);
        if($scope==2)$marks = SurveillanceArticleMark::orderBy('created_at','desc')->with('author')->with('content')->with('dispose')->paginate($pageSize);
        if($scope==3)$marks = SurveillanceExamMark::orderBy('created_at','desc')->with('author')->with('content')->with('dispose')->paginate($pageSize);
        if($scope==4)$marks = GroupMark::orderBy('created_at','desc')->with('author')->with('content')->with('dispose')->paginate($pageSize);
        return $marks = [
            'marks' => $marks
        ];
    }

    public function markReactRecord(Request $request) {
        $data = $request->data;
        $scope = $data['scope'];
        $id = $data['id'];
        $user_id = $data['user_id'];
        $result = 0;
        if($scope==1)$result=SurveillanceMarkReactRecord::where('mark_id',$id)->get();
        if($scope==2)$result=SurveillanceArticleMarkReactRecord::where('mark_id',$id)->get();
        if($scope==3)$result=SurveillanceExamMarkReactRecord::where('mark_id',$id)->get();
        if($scope==4)$result=GroupMarkRecord::where('mark_id',$id)->get();
        return ['check' => $result?true:false,'record' => $result];
    }
    public function committeeMarkReact(Request $request) {
        $data = $request->data;
        $user = Auth::user();
        $scope = $data['scope'];
        $mark_id = $data['id'];
        $user_id = $data['user_id'];
        $stand = $data['stand'];
        $remark = $data['remark']??'overthrow';
        $createtime = Carbon::now();
        $result = false;
        $status = 0;
        $rs = '';
        if($scope==1){
            $result=SurveillanceMarkReactRecord::newMarkReactRecord($mark_id,$user_id,$stand,$remark,$createtime);
            $mark = SurveillanceMark::find($mark_id);
            // 由于listener不起作用，因此暂时将功能放在这里（我也觉得是否listeners添加的太多为了？）
            if($user->id==$mark->user_id && $stand == 3 && $user->decrement('gold')) {
                $mark->update(['status'=>3]);
            }
            if($result) {
                $status = $mark->status;
                $rs = SurveillanceMarkReactRecord::where('mark_id',$mark_id)->with('getOperator')->get();
            }
        }else if($scope==2){
            $result=SurveillanceArticleMarkReactRecord::newMarkReactRecord($mark_id,$user_id,$stand,$remark,$createtime);
            $mark = SurveillanceArticleMark::find($mark_id);
            if($user->id==$mark->user_id && $stand == 3 && $user->decrement('gold')) {
                $mark->update(['status'=>3]);
            }
            if($result) {
                $status = $mark->status;
                $rs = SurveillanceArticleMarkReactRecord::where('mark_id',$mark_id)->with('getOperator')->get();
            }
        }else if($scope==3){
            $result=SurveillanceExamMarkReactRecord::newMarkReactRecord($mark_id,$user_id,$stand,$remark,$createtime);
            $mark = SurveillanceExamMark::find($mark_id);
            if($user->id==$mark->user_id && $stand == 3 && $user->decrement('gold')) {
                $mark->update(['status'=>3]);
            }
            if($result) {
                $status = $mark->status;
                $rs = SurveillanceExamMarkReactRecord::where('mark_id',$mark_id)->with('getOperator')->get();
            }
        }else if($scope==4){
            $result=GroupMarkRecord::newMarkReactRecord($mark_id,$user_id,$stand,$remark,$createtime);
            $mark = SurveillanceGroupeMark::find($mark_id);
            if($user->id==$mark->user_id && $stand == 3 && $user->decrement('gold')) {
                $mark->update(['status'=>3]);
            }
            if($result) {
                $status = $mark->status;
                $rs = GroupMarkRecord::where('mark_id',$mark_id)->with('getOperator')->get();
            }
        }
        return ['success' => $result?true:false,'status'=>$status,'records'=>$rs];
    }

    public function committeeSurveillanceWarnings(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$pageSize = $data['pageSize'];
    	if($scope==1)$warnings = SurveillanceWarning::orderBy('createtime','desc')->with('author')->with('content')->paginate($pageSize);
        if($scope==2)$warnings = SurveillanceArticleWarning::orderBy('createtime','desc')->with('author')->with('content')->paginate($pageSize);
        if($scope==3)$warnings = SurveillanceExamWarning::orderBy('createtime','desc')->with('author')->with('content')->paginate($pageSize);
        if($scope==4)$warnings = GroupWarning::orderBy('createtime','desc')->with('author')->with('content')->paginate($pageSize);
        return $marks = [
            'warnings' => $warnings
        ];
    }
}
