<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryDiscussion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDiscussion;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\EntryDiscussion\EntryAdvise;
use App\Home\Encyclopedia\EntryDiscussion\EntryDiscussionEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DiscussionCreateController extends Controller
{
    //词条讨论话题的创建
    public function discussion_create(Request $request){
    	
    	$id = $request->entry_id;
		$title = $request->title;
		$comment = $request->discussion;
		$author_id = auth('api')->user()->id;
        $author = auth('api')->user()->username;
        $standPoint = $request->standPoint;
		//反对的有效期是30天，有有效的反对讨论内容在，不能发起评审计划，对于普通讨论内容，不需要deadline
        $deadline = Carbon::now()->addDays(30);
        $round = 1;
        //立场值为1，代表反对
        if($standPoint == 1){
        	//将反对内容写入反对讨论表
            // 由于目前不存在读写一致性的问题，所以我们仍然采用优先写入并读取redis的方法
        	$result = EntryOpponent::opponentAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
        }elseif($standPoint == 2){
        	//将反对内容写入反对讨论表
        	$result = EntryAdvise::adviseAdd($id,$deadline,$title,$comment,0,$author_id,$author,$round);
        }elseif($standPoint == 3){
        	//将反对内容写入反对讨论表
        	$result = EntryDiscussion::discussionAdd($id,$title,$comment,0,$author_id,$author);
        }
        return ['success'=>$result? true:false];
    }
}
