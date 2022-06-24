<?php

namespace App\Http\Controllers\Api\CommonShare;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Events\Encyclopedia\EntrySummaryModifiedEvent;
use App\Home\Publication\Article;
use App\Events\Publication\ArticleSummaryModifiedEvent;
use App\Events\Examination\ExamSummaryModifiedEvent;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use Illuminate\Support\Facades\Auth;

class AbstractController extends Controller
{
    public function abstractModify(Request $request) {
    	$success = false;
    	$msg = '';
    	$id = $request->id;
    	$scope = $request->scope;
    	$summary = $request->abstract;
        $user_id = auth('api')->user()->id;
 		//修改摘要
	 	if($scope==1){
	 		$result = Entry::where('id',$id)->update([
	 			'summary'    => $summary,
            'lasteditor_id' => $user_id
	 		]);
	        event(new EntrySummaryModifiedEvent(Entry::find($id)));
	        $msg = ($result ? '修改成功！' : '');
 		}elseif($scope==2){
 			$result = Article::where('id',$id)->update([
	 			'summary'    => $summary,
            	'lasteditor_id' => $user_id
	 		]);
	 		event(new ArticleSummaryModifiedEvent(Article::find($id)));
 		}elseif($scope==3){
            $result = Exam::where('id',$id)->update([
                'summary'    => $summary,
                'lasteditor_id' => $user_id
            ]);
            event(new ExamSummaryModifiedEvent(Exam::find($id)));
        }elseif($scope==4){
            $result = Group::introModify($id,$summary);
        }

    	return $res = [
    		'success'=>$result ? true:false,
    		'msg' => $msg ? '修改成功！' : ''
    	];
    }
}
