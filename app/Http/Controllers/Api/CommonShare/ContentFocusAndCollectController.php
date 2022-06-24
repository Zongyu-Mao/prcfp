<?php

namespace App\Http\Controllers\Api\CommonShare;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry\EntryFocusUser;
use App\Home\Encyclopedia\Entry\EntryCollectUser;
use App\Home\Publication\Article\ArticleFocusUser;
use App\Home\Publication\Article\ArticleCollectUser;
use App\Home\Examination\Exam\ExamFocusUser;
use App\Home\Examination\Exam\ExamCollectUser;
use App\Home\Organization\Group\GroupFocusUser;
use Illuminate\Support\Facades\Auth;
use DB;

class ContentFocusAndCollectController extends Controller
{
    //关注
    public function contentFocus(Request $request){
    	$data = $request->data;
		$id = $data['id'];
		$scope = $data['scope'];
		$type = $data['type'];
		$result = false;
		$user_id = auth('api')->user()->id;
		if($scope==1){
			if($type==1 && !EntryFocusUser::where([['user_id',$user_id],['eid',$id]])->exists())$result = EntryFocusUser::entryFocus($user_id,$id);
			if($type==2 && EntryFocusUser::where([['user_id',$user_id],['eid',$id]])->exists())$result = EntryFocusUser::entryFocusCancel($user_id,$id);
			if($type==3 && !EntryCollectUser::where([['user_id',$user_id],['eid',$id]])->exists())$result = EntryCollectUser::entryCollect($user_id,$id);
			if($type==4 && EntryCollectUser::where([['user_id',$user_id],['eid',$id]])->exists())$result = EntryCollectUser::entryCollectCancel($user_id,$id);
		}elseif($scope==2){
			if($type==1 && !ArticleFocusUser::where([['user_id',$user_id],['article_id',$id]])->exists())$result = ArticleFocusUser::articleFocus($user_id,$id);
			if($type==2 && ArticleFocusUser::where([['user_id',$user_id],['article_id',$id]])->exists())$result = ArticleFocusUser::articleFocusCancel($user_id,$id);
			if($type==3 && !ArticleCollectUser::where([['user_id',$user_id],['article_id',$id]])->exists())$result = ArticleCollectUser::articleCollect($user_id,$id);
			if($type==4 && ArticleCollectUser::where([['user_id',$user_id],['article_id',$id]])->exists())$result = ArticleCollectUser::articleCollectCancel($user_id,$id);
		}elseif($scope==3){
			if($type==1 && !ExamFocusUser::where([['user_id',$user_id],['exam_id',$id]])->exists())$result = ExamFocusUser::examFocus($user_id,$id);
			if($type==2 && ExamFocusUser::where([['user_id',$user_id],['exam_id',$id]])->exists())$result = ExamFocusUser::examFocusCancel($user_id,$id);
			if($type==3 && !ExamCollectUser::where([['user_id',$user_id],['exam_id',$id]])->exists())$result = ExamCollectUser::examCollect($user_id,$id);
			if($type==4 && ExamCollectUser::where([['user_id',$user_id],['exam_id',$id]])->exists())$result = ExamCollectUser::examCollectCancel($user_id,$id);
		}elseif($scope==4){
			if($type==1 && !GroupFocusUser::where([['user_id',$user_id],['gid',$id]])->exists())$result = GroupFocusUser::groupFocus($user_id,$id);
			if($type==2 && GroupFocusUser::where([['user_id',$user_id],['gid',$id]])->exists())$result = GroupFocusUser::groupFocusCancel($user_id,$id);
		}
		return ['success'=>$result? true:false];
    }
}
