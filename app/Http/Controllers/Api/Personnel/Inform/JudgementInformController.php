<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Medal;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\JudgementInform;
use App\Home\Personnel\JudgementInform\JudgementInformMEdal;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryReview\EntryReviewAdvise;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\ArticleReview\ArticleReviewAdvise;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDebate;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\ExamReview\ExamReviewAdvise;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamDiscussion;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDebate;
use App\Home\Organization\Group\GroupDoc;
use App\Models\Committee\CommitteeDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class JudgementInformController extends Controller
{
    //裁决内容的举报：百科1评选中立2评选支持3评选建议4评选反对5求助6求助帮助、7讨论、8建议、9反对、10攻方11辩方12裁判
    public function judgementInform(Request $request){
    	$scope = $request->scope;
    	$judgement_id = $request->id;
    	$judgement_title = $request->name;
    	$judgementect_user_id = '';
    	switch($scope)
    	{
    		case 1:
    		// 评审的中立发言
    		$judgement = EntryReviewDiscussion::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/encyclopedia/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 2:
    		// 评审的支持发言
    		$judgement = EntryReviewDiscussion::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/encyclopedia/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 3:
    		$judgement = EntryReviewAdvise::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/encyclopedia/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 4:
    		$judgement = EntryReviewOpponent::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/encyclopedia/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 5:
    		// 求助内容
    		$judgement = EntryResort::find($judgement_id);
    		$url_obj = $judgement->getContent;
    		$remark = '百科词条《'.$url_obj->title.'》的求助计划<'.$judgement->title.'>';
    		$url = '/encyclopedia/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 6:
    		// 帮助内容
    		$judgement = EntryResort::find($judgement_id);
    		$url_obj = $judgement->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的帮助内容<'.$judgement->title.'>';
    		$url = '/encyclopedia/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 7:
    		// 主内容讨论内容
    		$judgement = EntryDiscussion::find($judgement_id);
    		$url_obj = $judgement->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的讨论内容（讨论）<'.$judgement->title.'>';
    		$url = '/encyclopedia/discussion/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 8:
    		// 主内容建议内容
    		$judgement = EntryAdvise::find($judgement_id);
    		$url_obj = $judgement->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的讨论内容（建议）<'.$judgement->title.'>';
    		$url = '/encyclopedia/discussion/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 9:
    		// 主内容反对内容
    		$judgement = EntryOpponent::find($judgement_id);
    		$url_obj = $judgement->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的讨论内容（反对）<'.$judgement->title.'>';
    		$url = '/encyclopedia/discussion/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 10:
    		// 攻方发言注意id可能相同！！
    		$judgement = EntryDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->Aauthor_id;
    		$url_obj = $judgement->getEntry;
    		$remark = '百科词条《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(A)';
    		$url = '/encyclopedia/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 11:
    		// 辩方发言注意区别，同一个id
    		$judgement = EntryDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->Bauthor_id;
    		$url_obj = $judgement->getContent;
    		$remark = '百科词条《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(B)';
    		$url = '/encyclopedia/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 12:
    		// 裁判发言，id可能相同
    		$judgement = EntryDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->referee_id;
    		$url_obj = $judgement->getContent;
    		$remark = '百科词条《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(R)';
    		$url = '/encyclopedia/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 13:
    		// 评审的中立发言
    		$judgement = ArticleReviewDiscussion::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/publication/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 14:
    		// 评审的支持发言
    		$judgement = ArticleReviewDiscussion::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/publication/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 15:
    		$judgement = ArticleReviewAdvise::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/publication/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 16:
    		$judgement = ArticleReviewOpponent::find($judgement_id);
    		$parent = $judgement->getReview;
    		$url_obj = $parent->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
    		$url = '/publication/review/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 17:
    		// 求助内容
    		$judgement = ArticleResort::find($judgement_id);
    		$url_obj = $judgement->getContent;
    		$remark = '著作《'.$url_obj->title.'》的求助计划<'.$judgement->title.'>';
    		$url = '/publication/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 18:
    		// 帮助内容
    		$judgement = ArticleResort::find($judgement_id);
    		$url_obj = $judgement->getContent;
    		$remark = '著作《'.$url_obj->title.'》的求助计划<'.$judgement->title.'>';
    		$url = '/publication/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 19:
    		// 主内容讨论内容
    		$judgement = ArticleDiscussion::find($judgement_id);
    		$url_obj = $judgement->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的讨论内容（讨论）<'.$judgement->title.'>';
    		$url = '/publication/resort/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 20:
    		// 主内容建议内容
    		$judgement = ArticleAdvise::find($judgement_id);
    		$url_obj = $judgement->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的讨论内容（建议）<'.$judgement->title.'>';
    		$url = '/publication/debate/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 21:
    		// 主内容反对内容
    		$judgement = ArticleOpponent::find($judgement_id);
    		$url_obj = $judgement->getArticle;
    		$remark = '著作《'.$url_obj->title.'》的讨论内容（反对）<'.$judgement->title.'>';
    		$url = '/publication/cooperation/'.$url_obj->id.'/'.$url_obj->title;
    		break;
    		case 22:
    		// 攻方发言注意id可能相同！！
    		$judgement = ArticleDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->Aauthor_id;
    		$url_obj = $judgement->getContent;
    		$remark = '著作《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(A)';
    		$url = '/publication/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 23:
    		// 辩方发言注意区别，同一个id
    		$judgement = ArticleDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->Bauthor_id;
    		$url_obj = $judgement->getContent;
    		$remark = '著作《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(B)';
    		$url = '/publication/resort/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 24:
    		// 裁判发言，id可能相同
    		$judgement = ArticleDebate::find($judgement_id);
    		$judgementect_user_id = $judgement->referee_id;
    		$url_obj = $judgement->getContent;
    		$remark = '著作《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(R)';
    		$url = '/publication/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
    		break;
    		case 25:
            // 评审的中立发言
            $judgement = ExamReviewDiscussion::find($judgement_id);
            $parent = $judgement->getReview;
            $url_obj = $parent->getExam;
            $remark = '试卷《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
            $url = '/examination/review/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 26:
            // 评审的支持发言
            $judgement = ExamReviewDiscussion::find($judgement_id);
            $parent = $judgement->getReview;
            $url_obj = $parent->getExam;
            $remark = '试卷《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
            $url = '/examination/review/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 27:
            $judgement = ExamReviewAdvise::find($judgement_id);
            $parent = $judgement->getReview;
            $url_obj = $parent->getExam;
            $remark = '试卷《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
            $url = '/examination/review/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 28:
            $judgement = ExamReviewOpponent::find($judgement_id);
            $parent = $judgement->getReview;
            $url_obj = $parent->getExam;
            $remark = '试卷《'.$url_obj->title.'》的评审计划<'.$parent->title.'>';
            $url = '/examination/review/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 29:
            // 求助内容
            $judgement = ExamResort::find($judgement_id);
            $url_obj = $judgement->getContent;
            $remark = '试卷《'.$url_obj->title.'》的求助计划<'.$judgement->title.'>';
            $url = '/examination/resort/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 30:
            // 帮助内容
            $judgement = ExamResort::find($judgement_id);
            $url_obj = $judgement->getContent;
            $remark = '试卷《'.$url_obj->title.'》的求助计划<'.$judgement->title.'>';
            $url = '/examination/resort/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 31:
            // 主内容讨论内容
            $judgement = ExamDiscussion::find($judgement_id);
            $url_obj = $judgement->getExam;
            $remark = '试卷《'.$url_obj->title.'》的讨论内容（讨论）<'.$judgement->title.'>';
            $url = '/examination/resort/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 32:
            // 主内容建议内容
            $judgement = ExamAdvise::find($judgement_id);
            $url_obj = $judgement->getExam;
            $remark = '试卷《'.$url_obj->title.'》的讨论内容（建议）<'.$judgement->title.'>';
            $url = '/examination/debate/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 33:
            // 主内容反对内容
            $judgement = ExamOpponent::find($judgement_id);
            $url_obj = $judgement->getExam;
            $remark = '试卷《'.$url_obj->title.'》的讨论内容（反对）<'.$judgement->title.'>';
            $url = '/examination/cooperation/'.$url_obj->id.'/'.$url_obj->title;
            break;
            case 34:
            // 攻方发言注意id可能相同！！
            $judgement = ExamDebate::find($judgement_id);
            $judgementect_user_id = $judgement->Aauthor_id;
            $url_obj = $judgement->getContent;
            $remark = '试卷《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(A)';
            $url = '/examination/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
            break;
            case 35:
            // 辩方发言注意区别，同一个id
            $judgement = ExamDebate::find($judgement_id);
            $judgementect_user_id = $judgement->Bauthor_id;
            $url_obj = $judgement->getContent;
            $remark = '试卷《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(B)';
            $url = '/examination/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
            break;
            case 36:
            // 裁判发言，id可能相同
            $judgement = ExamDebate::find($judgement_id);
            $judgementect_user_id = $judgement->referee_id;
            $url_obj = $judgement->getContent;
            $remark = '试卷《'.$url_obj->title.'》的攻辩计划<'.$judgement->title.'>(R)';
            $url = '/examination/debate/'.$url_obj->id.'/'.$url_obj->title.'?type='.$judgement->type.'&type_id='.$judgement->type_id;
            break;
            case 37:
            $judgement = GroupDoc::find($obj_id);//这里是举报的组织文档 不是组织！！！！！！！
            $judgementect_user_id = $judgement->creator_id;
            $remark = '组织文档《'.$judgement->title.'》';
            $url = '/organization/groupDocDetail?id='.$obj_id.'&name='.$obj_title;
            break;
            case 38:
            $judgement = CommitteeDocument::find($obj_id);//这里是举报的组织文档 不是组织！！！！！！！
            $judgementect_user_id = $judgement->creator_id;
            $remark = '管理组文档《'.$judgement->title.'》';
            $url = '/management/committee-document/'.$obj_id.'/'.$obj_title;
            break;
            default:
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
		if(!$judgementect_user_id){
			$judgementect_user_id = $judgement->author_id;
		}
		// status需要重新计划************************************************************
		$weight<10 ? $status = 0 : $status = 3;

		$createtime = Carbon::now();
        if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = JudgementInform::informAdd($author_id,$judgementect_user_id,$title,$weight,$content,$url,$remark,$scope,$judgement_id,$status);
        }
		
		for($i=0;$i<count($medals);$i++){
			JudgementInformMedal::create([
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

    public function judgementContentCheck(Request $request) {
        $input = $request->data;
        $scope = $input['scope'];
        $judgement_id = $input['id'];
        $judgement_title = $input['name'];
        $judgement = false;
        switch($scope)
        {
            case 1:
            // 评审的中立发言
            $judgement = EntryReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 2:
            // 评审的支持发言
            $judgement = EntryReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 3:
            $judgement = EntryReviewAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 4:
            $judgement = EntryReviewOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 5:
            // 求助内容
            $judgement = EntryResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 6:
            // 帮助内容
            $judgement = EntryResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 7:
            // 主内容讨论内容
            $judgement = EntryDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 8:
            // 主内容建议内容
            $judgement = EntryAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 9:
            // 主内容反对内容
            $judgement = EntryOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 10:
            // 攻方发言注意id可能相同！！
            $judgement = EntryDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 11:
            // 辩方发言注意区别，同一个id
            $judgement = EntryDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 12:
            // 裁判发言，id可能相同
            $judgement = EntryDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 13:
            // 评审的中立发言
            $judgement = ArticleReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 14:
            // 评审的支持发言
            $judgement = ArticleReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 15:
            $judgement = ArticleReviewAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 16:
            $judgement = ArticleReviewOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 17:
            // 求助内容
            $judgement = ArticleResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 18:
            // 帮助内容
            $judgement = ArticleResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 19:
            // 主内容讨论内容
            $judgement = ArticleDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 20:
            // 主内容建议内容
            $judgement = ArticleAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 21:
            // 主内容反对内容
            $judgement = ArticleOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 22:
            // 攻方发言注意id可能相同！！
            $judgement = ArticleDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 23:
            // 辩方发言注意区别，同一个id
            $judgement = ArticleDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 24:
            // 裁判发言，id可能相同
            $judgement = ArticleDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 25:
            // 评审的中立发言
            $judgement = ExamReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 26:
            // 评审的支持发言
            $judgement = ExamReviewDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 27:
            $judgement = ExamReviewAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 28:
            $judgement = ExamReviewOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 29:
            // 求助内容
            $judgement = ExamResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 30:
            // 帮助内容
            $judgement = ExamResort::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 31:
            // 主内容讨论内容
            $judgement = ExamDiscussion::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 32:
            // 主内容建议内容
            $judgement = ExamAdvise::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 33:
            // 主内容反对内容
            $judgement = ExamOpponent::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 34:
            // 攻方发言注意id可能相同！！
            $judgement = ExamDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 35:
            // 辩方发言注意区别，同一个id
            $judgement = ExamDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 36:
            // 裁判发言，id可能相同
            $judgement = ExamDebate::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 37:
            $judgement = GroupDoc::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            case 38:
            $judgement = CommitteeDocument::where([['id',$judgement_id],['title',$judgement_title]])->exists();
            break;
            default:
            break;

        }
        return  [
            'check'   => $judgement ? true:false
        ];
    }
}
