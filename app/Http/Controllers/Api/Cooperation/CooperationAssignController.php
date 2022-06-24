<?php

namespace App\Http\Controllers\Api\Cooperation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\ExamCooperation;
use App\Events\Publication\ArticleCooperation\ArticleCooperationAssignModifiedEvent;
use App\Events\Examination\ExamCooperation\ExamCooperationAssignModifiedEvent;

class CooperationAssignController extends Controller
{
    public function assign(Request $request){
    	$id = $request->id;
    	$scope = $request->scope;
    	$assign = $request->assign;
    	$result = false;
        //接收改过的任务描述并写入数据表
        // return $data1;
		if($scope==1){
			$result = EntryCooperation::where('id',$id)->update([
				'assign' => $assign
			]);	
		}elseif($scope==2){
			$result = ArticleCooperation::where('id',$id)->update([
					'assign' => $assign,
				]);
			event(new ArticleCooperationAssignModifiedEvent(ArticleCooperation::find($id)));
		}elseif($scope==3){
			$result = ExamCooperation::where('id',$id)->update([
					'assign' => $assign,
				]);
			event(new ExamCooperationAssignModifiedEvent(ExamCooperation::find($id)));
		}
		return ['success'=>$result ? true : false];
		
    }
}
