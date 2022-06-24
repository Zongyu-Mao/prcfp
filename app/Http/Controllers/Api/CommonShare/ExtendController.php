<?php

namespace App\Http\Controllers\Api\CommonShare;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Encyclopedia\Entry\Extended\EntryExtendedEntryReading;
use App\Home\Encyclopedia\Entry\Extended\EntryExtendedArticleReading;
use App\Home\Encyclopedia\Entry\Extended\EntryExtendedExamReading;
use App\Home\Publication\Article\ExtendedReading\ArticleExtendedEntryReading;
use App\Home\Publication\Article\ExtendedReading\ArticleExtendedArticleReading;
use App\Home\Publication\Article\ExtendedReading\ArticleExtendedExamReading;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ExtendController extends Controller
{
    //
    public function extend_reading(Request $request) {
    	$id = $request->id;
    	$user_id = $request->user_id;
    	$title = $request->title;
    	$ex_title = $request->ex_title;
    	$scope = $request->scope;
    	$type = $request->type;
    	$creator_id = auth('api')->user()->id;
    	$result = false;
        $msg = '';
    	$data = '';
        $ex = [];
    	if($user_id!=$creator_id){
    		return $res = [
	    		'success'=>$result,
	    		'msg' => '用户不合法'
	    	];
    	}
    	$extended_id=0;
    	if($type==1){
            $extended_id=Entry::where('title',$ex_title)->first()->id;
    	} elseif($type==2) {
    		$extended_id=Article::where('title',$ex_title)->first()->id;
    	} elseif($type==3) {
    		$extended_id=Exam::where('title',$ex_title)->first()->id;
    	}
    	// return $request;
    	if($extended_id) {
    		if($scope==1){
                $data = Entry::find($id);
				if($type==1 && !EntryExtendedEntryReading::where([['eid',$id],['extended_id',$extended_id]])->exists()){
	 			    $result = EntryExtendedEntryReading::entryExtendedAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedEntryReadings()->get();
	 		    }elseif($type==2 && !EntryExtendedArticleReading::where([['eid',$id],['extended_id',$extended_id]])->exists()){
	 		    	$result = EntryExtendedArticleReading::entryExtendedArticleAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedArticleReadings()->get();
	 		    }elseif($type==3 && !EntryExtendedExamReading::where([['eid',$id],['extended_id',$extended_id]])->exists()){
	 		    	$result = EntryExtendedExamReading::entryExtendedExamAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedExamReadings()->get();
	 		    }
			} elseif ($scope==2){
                $data = Article::find($id);
				if($type==1 && !ArticleExtendedEntryReading::where([['aid',$id],['extended_id',$extended_id]])->exists()){
	 			    $result = ArticleExtendedEntryReading::articleExtendedEntryAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedEntryReading()->get();
	 		    }elseif($type==2 && !ArticleExtendedArticleReading::where([['aid',$id],['extended_id',$extended_id]])->exists()){
	 		    	$result = ArticleExtendedArticleReading::articleExtendedArticleAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedArticleReading()->get();
	 		    }elseif($type==3 && !ArticleExtendedExamReading::where([['aid',$id],['extended_id',$extended_id]])->exists()){
	 		    	$result = ArticleExtendedExamReading::articleExtendedExamAdd($id,$extended_id,$creator_id);
                    if($result)$ex = $data->extendedExamReading()->get();
	 		    }
			}
    	} else {
            return $res = [
                'success'=>$result,
                'msg' => '内容不存在'
            ];
        }
		return $res = [
            'success'=>$result ? true:false,
    		'ex'=>$ex,
    		'msg' => $msg
    	];
            
    }

    public function extend_delete(Request $request) {
    	$id = $request->id;
    	$extended_id = $request->delete_id;
    	$scope = $request->scope;
    	$type = $request->type;
    	$user_id = $request->user_id;
    	$result = false;
    	$msg = '';
    	$auth_id = auth('api')->user()->id;
    	if($user_id!=$auth_id){
    		return $res = [
	    		'success'=>$result ? true:false,
	    		'msg' => '用户不合法！'
	    	];
    	}
        $ex = [];
    	if($scope==1){
            $data = Entry::find($id);
    		if($type==1){
	    		$result =EntryExtendedEntryReading::entryExtendedDelete($id,$extended_id);
                if($result)$ex = $data->extendedEntryReadings()->get();
	    	}elseif($type==2){
	    		$result =EntryExtendedArticleReading::entryExtendedArticleDelete($id,$extended_id);
                if($result)$ex = $data->extendedArticleReadings()->get();
	    	}elseif($type==3){
	    		$result =EntryExtendedExamReading::entryExtendedExamDelete($id,$extended_id);
                if($result)$ex = $data->extendedExamReadings()->get();
	    	}
    	}elseif($scope==2){
            $data = Article::find($id);
    		if($type==1){
	    		$result =ArticleExtendedEntryReading::articleExtendedEntryDelete($id,$extended_id);
                if($result)$ex = $data->extendedEntryReading()->get();
	    	}elseif($type==2){
	    		$result =ArticleExtendedArticleReading::articleExtendedArticleDelete($id,$extended_id);
                if($result)$ex = $data->extendedArticleReading()->get();
	    	}elseif($type==3){
	    		$result =ArticleExtendedExamReading::articleExtendedExamDelete($id,$extended_id);
                if($result)$ex = $data->extendedExamReading()->get();
	    	}
    	}
    	return $res = [
            'success'=>$result ? true:false,
    		'ex'=>$ex,
    		'msg' => $msg ? '修改成功！' : ''
    	];
    }

    public function extend_check(Request $request) {
    	// 这里只是check内容是否存在
    	$data = $request->data;
    	$scope = $request->scope;
    	$type = $request->type;
    	$check = false;
    	$msg = '';
    	if($type==1){
    		$check = Entry::where('title',$ex_title)->exists();
    	}elseif($type==2){
    		$check = Article::where('title',$ex_title)->exists();
    	}elseif($type==3){
    		$check = Exam::where('title',$ex_title)->exists();
    	}
    	return $res = [
    		'success'=>$check ? true:false,
    		'title'=>$ex_title,
    		'msg' => $msg ? '修改成功！' : ''
    	];
    }

    
}
