<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Medal;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\MessageInform\MessageInformMEdal;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationMessage;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryResort\EntryResortSupportComment;
use App\Home\Encyclopedia\EntryDebate\EntryDebateComment;
use App\Home\Publication\ArticleCooperation\ArticleCooperationMessage;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\ArticleResort\ArticleResortSupportComment;
use App\Home\Publication\ArticleDebate\ArticleDebateComment;
use App\Home\Examination\ExamCooperation\ExamCooperationMessage;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\ExamResort\ExamResortSupportComment;
use App\Home\Examination\ExamDebate\ExamDebateComment;
use App\Home\Organization\Group\GroupDoc\GroupDocComment;
use App\Models\Committee\CommitteeDocument;
use App\Models\Committee\CommitteeDocumentComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MessageInformController extends Controller
{
    //留言的举报1协作留言、2评选留言、3求助内容留言、4攻辩留言、5组织文档留言
    public function messageInform(Request $request){
    	$scope = $request->scope;
    	$obj_id = $request->id;
    	$obj_title = $request->name;
    	switch($scope)
    	{
    		case 1:
    		$message = EntryCooperationMessage::find($obj_id);
    		$parent = $message->getCooperation;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的协作计划<'.$parent->title.'>';
    		$url = '/encyclopedia/cooperation/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 2:
    		$message = EntryReviewDiscussion::find($obj_id);
    		$parent = $message->getReview;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/encyclopedia/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 3:
    		$message = EntryResortSupportComment::find($obj_id);
    		$parent = $message->getResort;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的求助计划<'.$parent->title.'>';
    		$url = '/encyclopedia/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 4:
    		$message = EntryDebateComment::find($obj_id);
    		$parent = $message->getDebate;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的攻辩<'.$parent->title.'>';
    		$url = '/encyclopedia/debate/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 5:
    		$message = ArticleCooperationMessage::find($obj_id);
    		$parent = $message->getCooperation;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的协作计划<'.$parent->title.'>';
    		$url = '/publication/cooperation/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 6:
    		$message = ArticleReviewDiscussion::find($obj_id);
    		$parent = $message->getReview;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/publication/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 7:
    		$message = ArticleResortSupportComment::find($obj_id);
    		$parent = $message->getResort;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的求助计划<'.$parent->title.'>';
    		$url = '/publication/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 8:
    		$message = ArticleDebateComment::find($obj_id);
    		$parent = $message->getDebate;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的攻辩计划<'.$parent->title.'>';
    		$url = '/publication/debate/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 9:
    		$message = ExamCooperationMessage::find($obj_id);
    		$parent = $message->getCooperation;
    		$url_obj = $parent->getExam;
    		$remark = '试卷《'.$url_obj->title.'》的协作计划<'.$parent->title.'>';
    		$url = '/examination/cooperation/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 10:
    		$message = ExamReviewDiscussion::find($obj_id);
    		$parent = $message->getReview;
    		$url_obj = $parent->getExam;
    		$remark = '试卷《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/examination/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 11:
    		$message = ExamResortSupportComment::find($obj_id);
    		$parent = $message->getResort;
    		$url_obj = $parent->getExam;
    		$remark = '试卷《'.$url_obj->title.'》的求助计划<'.$parent->title.'>';
    		$url = '/examination/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 12:
    		$message = ExamDebateComment::find($obj_id);
    		$parent = $message->getDebate;
    		$url_obj = $parent->getExam;
    		$remark = '试卷《'.$url_obj->title.'》的攻辩计划<'.$parent->title.'>';
    		$url = '/examination/debate/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 13:
    		$message = GroupDocComment::find($obj_id);
    		$parent = $message->getDoc;
    		$url_obj = $parent->getGroup;
    		$remark = '组织《'.$url_obj->title.'》的文档<'.$parent->title.'>';
    		$url = '/organization/groupDocDetail?id='.$url_obj->id.'&name='.$url_obj->title;
    		break;
            case 14:
            $message = CommitteeDocumentComment::find($obj_id);
            $parent = $message->document;
            $url_obj = $parent->comittee;
            $remark = '管理组《'.$url_obj->title.'》的文档<'.$parent->title.'>';
            $url = '/management/committee-document/'.$url_obj->id.'/'.$url_obj->title;
            break;
    	}

		$title = $request->title;
		$content = $request->content;
		$medals = $request->medalArr;
		$result = false;
		$weight = 0;
		for($i=0;$i<count($medals);$i++){
			$weight += Medal::find($medals[$i])->weight;
		}
		$user = auth('api')->user();
        $author_id = $user->id;

		$object_user_id = $message->author_id;
		// status需要重新计划************************************************************
		$weight<10 ? $status = 0 : $status = 3;

		$createtime = Carbon::now();
        if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = MessageInform::informAdd($author_id,$object_user_id,$title,$weight,$content,$url,$remark,$scope,$obj_id,$status);
        }
		
		for($i=0;$i<count($medals);$i++){
			MessageInformMedal::create([
				'inform_id'	=> $result->id,
				'medal_id'	=> $medals[$i],
				'createtime'	=> $createtime
			]);
		}
		return [
            'inform'   => $result,
    		'success'	=> $result? true:false
    	];
    }

    // 确认内容是否存在
    public function messageContentCheck(Request $request) {
        $input = $request->data;
        $scope = $input['scope'];
        $obj_id = $input['id'];
        $obj_title = $input['name'];
        $message = false;
        switch($scope)
        {
            case 1:
            $message = EntryCooperationMessage::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 2:
            $message = EntryReviewDiscussion::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 3:
            $message = EntryResortSupportComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 4:
            $message = EntryDebateComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 5:
            $message = ArticleCooperationMessage::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 6:
            $message = ArticleReviewDiscussion::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 7:
            $message = ArticleResortSupportComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 8:
            $message = ArticleDebateComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 9:
            $message = ExamCooperationMessage::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 10:
            $message = ExamReviewDiscussion::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 11:
            $message = ExamResortSupportComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 12:
            $message = ExamDebateComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 13:
            $message = GroupDocComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            case 14:
            $message = CommitteeDocumentComment::where([['id',$obj_id],['title',$obj_title]])->exists();
            break;
            default: 
            break;
        }
        return  [
            'check'   => $message ? true:false
        ];
    }
}
