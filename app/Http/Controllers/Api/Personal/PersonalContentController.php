<?php

namespace App\Http\Controllers\Api\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\ArticleResort;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Publication\ArticleDebate;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use App\Home\Organization\Group\GroupDoc;
use Illuminate\Support\Facades\Auth;

class PersonalContentController extends Controller
{
    public function ownContents(Request $request){
        $user = Auth::user();
        $data = $request->data;
        $user_id = $user->id;
        $pageSize = $data['pageSize'];
        $scope = $data['own_scope'];
        $attr = $data['attr_scope'];
        $contents = '';
        if($scope==1) {
        	if($attr=='manage') {
        		// 我的自管理词条
    			$contents = Entry::where('manage_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='part') {
		        // 我的普通协作
		        $cooperationIds = EntryCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();
		        $contents = EntryCooperation::whereIn('id',$cooperationIds)->with('getEntry')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='review') {
        		// 我的评审
        		$contents = EntryReview::where('initiate_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='resort') {
        		// 我的求助
        		$contents = EntryResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='help') {
        		// 我的求助
        		$contents = EntryResort::where([['author_id',$user_id],['pid','>',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='oppose') {
        		// 我的反对
		        $contents = EntryOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='advise') {
		        // 我的建议
		        $contents = EntryAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getEntry')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_a') {
        		// 我的攻辩
        		$contents = EntryDebate::where('Aauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_b') {
        		// 我的攻辩
        		$contents = EntryDebate::where('Bauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_r') {
        		// 我的攻辩
        		$contents = EntryDebate::where('referee_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	}
        } else if($scope==2) {
        	if($attr=='manage') {
        		// 我的自管理词条
    			$contents = Article::where('manage_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='part') {
		        // 我的普通协作
		        $cooperationIds = ArticleCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();;
		        $contents = ArticleCooperation::whereIn('id',$cooperationIds)->with('getArticle')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='review') {
        		// 我的评审
        		$contents = ArticleReview::where('initiate_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='resort') {
        		// 我的求助
        		$contents = ArticleResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='help') {
        		// 我的求助
        		$contents = ArticleResort::where([['author_id',$user_id],['pid','>',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='oppose') {
        		// 我的反对
		        $contents = ArticleOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='advise') {
		        // 我的建议
		        $contents = ArticleAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getArticle')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_a') {
        		// 我的攻辩
        		$contents = ArticleDebate::where('Aauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_b') {
        		// 我的攻辩
        		$contents = ArticleDebate::where('Bauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_r') {
        		// 我的攻辩
        		$contents = ArticleDebate::where('referee_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	}
        } else if($scope==3) {
        	if($attr=='manage') {
        		// 我的自管理词条
    			$contents = Exam::where('manage_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='part') {
		        // 我的普通协作
		        $cooperationIds = ExamCooperationUser::where('user_id',$user_id)->pluck('cooperation_id')->toArray();;
		        $contents = ExamCooperation::whereIn('id',$cooperationIds)->with('getExam')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='review') {
        		// 我的评审
        		$contents = ExamReview::where('initiate_id',$user_id)->with('getExam')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='resort') {
        		// 我的求助
        		$contents = ExamResort::where([['author_id',$user_id],['pid',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='help') {
        		// 我的求助
        		$contents = ExamResort::where([['author_id',$user_id],['pid','>',0]])->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='oppose') {
        		// 我的反对
		        $contents = ExamOpponent::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getExam')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='advise') {
		        // 我的建议
		        $contents = ExamAdvise::where('author_id',$user_id)->orWhere('recipient_id',$user_id)->with('getExam')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_a') {
        		// 我的攻辩
        		$contents = ExamDebate::where('Aauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_b') {
        		// 我的攻辩
        		$contents = ExamDebate::where('Bauthor_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='debate_r') {
        		// 我的攻辩
        		$contents = ExamDebate::where('referee_id',$user_id)->with('getContent')->orderBy('created_at','desc')->paginate($pageSize);
        	}
        } else if($scope==4) {
        	if($attr=='manage') {
    			$contents = Group::where('manage_id',$user_id)->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='part') {
		        $involve = GroupUser::where('user_id',$user_id)->pluck('gid')->toArray();;
		        $contents = Group::whereIn('id',$involve)->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='document') {
        		$contents = GroupDoc::where('creator_id',$user_id)->with('getGroup')->orderBy('created_at','desc')->paginate($pageSize);
        	}
        } else if($scope==5) {
        	if($attr=='entry') {
    			$contents = $user->getFocusEntries()->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='article') {
		        $contents = $user->getFocusArticles()->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='exam') {
        		$contents = $user->getFocusExams()->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='user') {
        		$contents = $user->getFocusUsers()->orderBy('created_at','desc')->paginate($pageSize);
        	}
        } else if($scope==6) {
        	if($attr=='entry') {
    			$contents = $user->getCollectEntries()->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='article') {
		        $contents = $user->getCollectArticles()->orderBy('created_at','desc')->paginate($pageSize);
        	} else if ($attr=='exam') {
        		$contents = $user->getCollectExams()->orderBy('created_at','desc')->paginate($pageSize);
        	}
        }
        return ['contents'=>$contents];
    }
}
