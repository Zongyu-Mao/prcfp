<?php

namespace App\Http\Controllers\Api\Discussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Home\Publication\ArticleDiscussion;
use App\Home\Publication\ArticleDiscussion\ArticleOpponent;
use App\Home\Publication\ArticleDiscussion\ArticleAdvise;
use App\Home\Examination\ExamDiscussion;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\ExamDiscussion\ExamAdvise;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DiscussionCreateController extends Controller
{
    //词条讨论话题的创建
    public function discussion_create(Request $request){
    	// 该id是归属id，如百科就是eid
    	$id = $request->id;
		$title = $request->title;
		$scope = $request->scope;
		$comment = $request->discussion;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $standPoint = $request->standPoint;
		//反对的有效期是30天，有有效的反对讨论内容在，不能发起评审计划，对于普通讨论内容，不需要deadline
        $deadline = Carbon::now()->addDays(30);
        $round = 1;
        $result = false;
        $bc = '';
        if($scope==1){
        	//立场值为1，代表反对
	        if($standPoint == 1){
	        	$result = EntryOpponent::opponentAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    // 这里全取而非在前端push是因为还没完全决定数据类型
                    $bc = EntryOpponent::where([['eid',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
                }
	        }elseif($standPoint == 2){
	        	$result = EntryAdvise::adviseAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    $bc = EntryAdvise::where([['eid',$id],['pid',0]])->orderBy('created_at','DESC')->with('allAdvise')->get();
                }
	        }elseif($standPoint == 3){
	        	$result = EntryDiscussion::discussionAdd($id,$title,$comment,0,$author_id,$author);
                if($result){
                    $bc = EntryDiscussion::where([['eid',$id],['pid',0]])->orderBy('created_at','DESC')->with('allDiscuss')->get();
                }
	        }
        }elseif ($scope==2){
        	if($standPoint == 1){
            	$result = ArticleOpponent::opponentAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    $bc = ArticleOpponent::where([['aid',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
                }
            }elseif($standPoint == 2){
            	$result = ArticleAdvise::adviseAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    $bc = ArticleAdvise::where([['aid',$id],['pid',0]])->with('allAdvise')->orderBy('created_at','DESC')->get();
                }
            }elseif($standPoint == 3){
            	$result = ArticleDiscussion::discussionAdd($id,$title,$comment,0,$author_id,$author);
                if($result){
                    $bc = ArticleDiscussion::where([['aid',$id],['pid',0]])->with('allDiscuss')->orderBy('created_at','DESC')->get();
                }
            }
        }elseif ($scope==3){
            if($standPoint == 1){
                $result = ExamOpponent::opponentAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    $bc = ExamOpponent::where([['exam_id',$id],['pid',0]])->with('allOppose')->orderBy('created_at','DESC')->get();
                }
            }elseif($standPoint == 2){
                $result = ExamAdvise::adviseAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
                if($result){
                    $bc = ExamAdvise::where([['exam_id',$id],['pid',0]])->with('allAdvise')->orderBy('created_at','DESC')->get();
                }
            }elseif($standPoint == 3){
                $result = ExamDiscussion::discussionAdd($id,$title,$comment,0,$author_id,$author);
                if($result){
                    $bc = ExamDiscussion::where([['exam_id',$id],['pid',0]])->with('allDiscuss')->orderBy('created_at','DESC')->get();
                }
            }
        }
        
        return ['success'=>$result? true:false,'backContent'=>$bc,'standPoint'=>$standPoint];
    }
}
