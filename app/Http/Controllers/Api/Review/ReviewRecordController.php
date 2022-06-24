<?php

namespace App\Http\Controllers\Api\Review;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryReview\EntryReviewOpponent;
use App\Home\Encyclopedia\EntryReview\EntryReviewDiscussion;
use App\Home\Encyclopedia\EntryReview\EntryReviewRecord;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleReview;
use App\Home\Publication\ArticleReview\ArticleReviewOpponent;
use App\Home\Publication\ArticleReview\ArticleReviewDiscussion;
use App\Home\Publication\ArticleReview\ArticleReviewRecord;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamReview\ExamReviewOpponent;
use App\Home\Examination\ExamReview\ExamReviewDiscussion;
use App\Home\Examination\ExamReview\ExamReviewRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ReviewRecordController extends Controller
{
    // 关闭评审计划
    public function reviewTerminate(Request $request){
        $data = $request->data;
        $id = $data['id'];
        $review_id = $data['review_id'];
        $scope = $data['scope'];
        $user_id = $data['user_id'];
        $type = $data['type'];
        $user = auth('api')->user()->only('id','username','gold');
        $result = false;
        $t = false;
        $msg = '';
        
        if($user_id===$user['id']){
            // type是1的，需要查看金币
            if($type==1){
                if($user['gold']<3){
                    $msg = '金币不足，无法操作！';
                    return $res = [
                        'success' => $result,
                        'msg' => $msg,
                    ];
                }
            }
            if($type==2) {
                if($scope==1) {
                    $opposeCount = EntryReviewOpponent::where([['rid',$review_id],['status',0]])->count();
                    $opposeDiscussionCount = EntryOpponent::where([['eid',$id],['status','0']])->count();
                    $debateCount = EntryDebate::where([['eid',$id],['status','0']])->count();
                } else if($scope==2) {
                    $opposeCount = ArticleReviewOpponent::where([['rid',$review_id],['status',0]])->count();
                    $opposeDiscussionCount = ArticleOpponent::where([['aid',$id],['status','0']])->count();
                    $debateCount = ArticleDebate::where([['aid',$id],['status','0']])->count();
                } else if($scope==3) {
                    $opposeCount = ExamReviewOpponent::where([['rid',$review_id],['status',0]])->count();
                    $opposeDiscussionCount = ExamOpponent::where([['exam_id',$id],['status','0']])->count();
                    $debateCount = ExamDebate::where([['exam_id',$id],['status','0']])->count();
                } 
            }
            if($scope===1) {
                $rid = Entry::find($id)->review_id;
                if($rid===$review_id)$review = EntryReview::find($review_id)->only('id','initiate_id','deadline');
            }else if ($scope===2) {
                $rid = Article::find($id)->review_id;
                if($rid===$review_id)$review = ArticleReview::find($review_id)->only('id','initiate_id','deadline');
            }else if ($scope===3) {
                $rid = Exam::find($id)->review_id;
                if($rid===$review_id)$review = ExamReview::find($review_id)->only('id','initiate_id','deadline');
            }
            // 这里要直接压掉金币了  另外，这里还要验证是否有有效的反对和攻辩
            if($review &&
                $user_id===$review['initiate_id'] &&
                (($type==1&&$user->update(['gold'=>$user->gold-3])) ||
                ($type==2&&!$opposeCount&&!$opposeDiscussionCount&&!$debateCount&&Carbon::now()>=$review['deadline']))
                ){
                if($scope==1 && Entry::reviewTerminate($id)){
                    $result = EntryReview::reviewUpdate($rid,$type); //注意还要清零entry的review_id
                } else if($scope==2 && Article::reviewTerminate($id)){
                    $result = ArticleReview::reviewUpdate($rid,$type);
                } else if($scope==3 && Exam::reviewTerminate($id)){
                    $result = ExamReview::reviewUpdate($rid,$type);
                }
                $msg = $result ? '操作成功！':'操作失败！';
            } else {
                if($user_id!=$review['initiate_id']) $msg.='请核对账户！';
                if($opposeCount) $msg.='存在有效的反对评审意见！';
                if($opposeDiscussionCount) $msg.='存在有效的反对讨论！';
                if($debateCount) $msg.='存在有效的攻辩计划！';
                if(Carbon::now()<$review['deadline']) $msg.='计划在正常期限，还未结束！';
            }
        } else {
            $msg = '账户出现问题！';
            return $res = [
                'success' => $result,
                'msg' => $msg,
            ];
        }
        return [
            'success'=>$result?true:false,
            'a'=>$user['username'],
            'b'=>(EntryReview::find($review_id)->deadline<Carbon::now()),
            'msg'=>$msg,
        ];
    }
    //评审反对意见的处理
    public function opponent(Request $request){
    	$id = $request->rid;
    	$scope = $request->scope;
    	// 此id是review的id
    	$result = false;
        $user = auth('api')->user();
    	$author_id = $user->id;
        $author = $user->username;

		$title = $request->title;
		$comment = $request->opponent;
		$standpoint=2;
		$createtime = Carbon::now();
        $os='';
        $ocs=0;
        if($title && $comment ){
        	if($scope==1 && !EntryReviewRecord::where([['review_id',$id],['user_id',$author_id]])->exists()){
        		// 添加评审的反对记录
	        	$mr = EntryReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
	            $result = EntryReviewOpponent::opponentAdd($id,$title,$comment,$author_id,$author);
                if($result){
                    $os = EntryReviewOpponent::where([['rid',$id],['pid',0]])->with('allOppose')->get();
                    $ocs = EntryReviewRecord::getOpposeNum($id);
                }
        	}elseif($scope==2){
                $mr = ArticleReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                $result = ArticleReviewOpponent::opponentAdd($id,$title,$comment,$author_id,$author);
                if($result){
                    $os = ArticleReviewOpponent::where([['rid',$id],['pid',0]])->with('allOppose')->get();
                    $ocs = ArticleReviewRecord::getOpposeNum($id);
                }
            }elseif($scope==3){
                $mr = ExamReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                $result = ExamReviewOpponent::opponentAdd($id,$title,$comment,$author_id,$author);
                if($result){
                    $os = ExamReviewOpponent::where([['rid',$id],['pid',0]])->with('allOppose')->get();
                    $ocs = ExamReviewRecord::getOpposeNum($id);
                }
            }
        	
        }
		return ['success'=>$result? true:false,'opponents'=>$os,'opposeNum'=>$ocs,'myReview'=>$mr];
    	
    }

    //处理反对意见的拒绝机制
    public function oppose_reject(Request $request){
    	// 该id是opponent的id
    	$input = $request->input;
    	$scope = $request->scope;
    	$id = $request->opponent_id;
    	$result = false;
        $opponent = EntryReviewOpponent::find($id);
		$rid = $opponent->rid;
		$title = $request->title;
		$comment = $request->reject;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
		$recipient = $opponent->author;
		$recipient_id = $opponent->author_id;
        if($title && $comment){
        	if($scope==1){
        		$result = EntryReviewOpponent::opponentReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result){
                    $os = EntryReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
                }
        	}elseif($scope==2){
                $result = ArticleReviewOpponent::opponentReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result){
                    $os = ArticleReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
                }
            }elseif($scope==3){
                $result = ExamReviewOpponent::opponentReject($rid,$title,$comment,$id,$author_id,$author,$recipient_id,$recipient);
                if($result){
                    $os = ExamReviewOpponent::where([['rid',$rid],['pid',0]])->with('allOppose')->get();
                }
            }
            
        }
        return ['success'=>$result? true:false,'opponents'=>$os];
    	
    }
    //处理反对意见的接受机制
    public function oppose_accept(Request $request){
    	// 该id仍未opponent的id
		$id = $request->opponent_id;
		$scope = $request->scope;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $status = 1;
		//接受了反对，更改反对意见为已接受，增加接受方
		if($scope==1){
            $o = EntryReviewOpponent::find($id);
			$result = EntryReviewOpponent::opponentAccept($id,$author_id,$author,$status);
            if($result){
                $os = EntryReviewOpponent::where([['rid',$o->rid],['pid',0]])->with('allOppose')->get();
            }
		}elseif ($scope==2) {
            $o = ArticleReviewOpponent::find($id);
            $result = ArticleReviewOpponent::opponentAccept($id,$author_id,$author,$status);
            if($result){
                $os = ArticleReviewOpponent::where([['rid',$o->rid],['pid',0]])->with('allOppose')->get();
            }
        }elseif ($scope==3) {
            $o = ExamReviewOpponent::find($id);
            $result = ExamReviewOpponent::opponentAccept($id,$author_id,$author,$status);
            if($result){
                $os = ExamReviewOpponent::where([['rid',$o->rid],['pid',0]])->with('allOppose')->get();
            }
        }
		
        return ['success'=>$result? true:false,'opponents'=>$os];
	}

    //处理支持内容区
 	public function support(Request $request){
		$id = $request->rid;
		$scope = $request->scope;
		$result = false;
		$comment = $request->support;
        $standpoint = 1;
        $createtime=Carbon::now();
        $author_id = auth('api')->user()->id;
		$author = auth('api')->user()->username;
        $title = $request->title;
        if($comment){
            //写入评论表，pid=0顶级评论，type=0支持评论
            if($scope==1){
            	// 添加评审的支持记录
	            $mr = EntryReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
	            $result = EntryReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);
                if($result) {
                    $ds =  EntryReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $agreeNum = EntryReviewRecord::getAgreeNum($id);
                }
            }elseif ($scope==2) {
                $mr = ArticleReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                $result = ArticleReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);
                if($result) {
                    $ds =  ArticleReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $agreeNum = ArticleReviewRecord::getAgreeNum($id);
                }
            }elseif ($scope==3) {
                $mr = ExamReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                $result = ExamReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);
                if($result) {
                    $ds =  ExamReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $agreeNum = ExamReviewRecord::getAgreeNum($id);
                }
            }
            
		}
		return ['success'=>$result? true:false,'discussions'=>$ds,'agreeNum'=>$agreeNum,'myReview'=>$mr];
 	}

 	//处理中立内容区
 	public function neutrality(Request $request){
		$id = $request->rid;
		$scope = $request->scope;
		$result = false;
		$comment = $request->neutrality;
        $standpoint = 3;
        $createtime=Carbon::now();
        $author_id = auth('api')->user() -> id;
		$author = auth('api')->user() -> username;
        $title = $request->input('title');
        if($comment){
        	if($scope==1){
            	// 添加评审记录
                $mr = EntryReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
	            //写入评论表，pid=0顶级评论，type=1中立
	            $result = EntryReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);	
                if($result) {
                    $ds =  EntryReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $neutralNum = EntryReviewRecord::getNeutralNum($id);
                }
        	}elseif ($scope==2) {
                $mr = ArticleReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                //写入评论表，pid=0顶级评论，type=1中立
                $result = ArticleReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);
                if($result) {
                    $ds =  ArticleReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $neutralNum = ArticleReviewRecord::getNeutralNum($id);
                }
            }elseif ($scope==3) {
                $mr = ExamReviewRecord::reviewRecordAdd($id,$author_id,$author,$standpoint,$createtime);
                //写入评论表，pid=0顶级评论，type=1中立
                $result = ExamReviewDiscussion::reviewCommentAdd($id,$author_id,$author,$title,$comment,'0',$standpoint);
                if($result) {
                    $ds =  ExamReviewDiscussion::where([['rid',$id],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                    $neutralNum = ExamReviewRecord::getNeutralNum($id);
                }
            }
            
		}
		return ['success'=>$result? true:false,'discussions'=>$ds,'neutralNum'=>$neutralNum,'myReview'=>$mr];
 	}

    //处理讨论区回复
    public function discuss_reply(Request $request){
    	$id = $request->id;
    	$rid = $request->rid;
    	$scope = $request->scope;
		$result = false;
        $comment = $request->reply;
        $author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $title = $request->title;
        $standpoint = 2;
        if($comment){
        	if($scope==1){
        		//写入评论表，pid=0顶级评论，type=0支持（回复默认全部为支持）
            	$result = EntryReviewDiscussion::reviewCommentAdd($rid,$author_id,$author,$title,$comment,$id,$standpoint);
                if($result) {
                    $ds =  EntryReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                }
        	}elseif($scope==2){
                $result = ArticleReviewDiscussion::reviewCommentAdd($rid,$author_id,$author,$title,$comment,$id,$standpoint);
                if($result) {
                    $ds =  ArticleReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                }
            }elseif($scope==3){
                $result = ExamReviewDiscussion::reviewCommentAdd($rid,$author_id,$author,$title,$comment,$id,$standpoint);
                if($result) {
                    $ds =  ExamReviewDiscussion::where([['rid',$rid],['pid',0]])->with('allDiscuss')->with('getAuthor')->get();
                }
            }
            
        }
        return ['success'=>$result? true:false,'discussions'=>$ds];
    }
}
