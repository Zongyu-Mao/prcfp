<?php

namespace App\Http\Controllers\Api\Encyclopedia\EntryReview;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class ReviewCreateController extends Controller
{
    //评审计划的创建
    public function create(Request $request){
        $id = $request->id;
        $input = $request->input;
    	$entry = Entry::find($id);
        $level = $entry->level;
        $result = false;
    	$kpi_count = EntryReview::where([['eid',$id],['status','0']])->exists() ? '1':'0';
    	//判断是否存在词条的反对意见
    	$enc_oppose_count = EntryOpponent::where([['eid',$id],['status','0']])->exists() ? '1':'0';
    	 
    	if($request->isMethod('post') && $kpi_count == '0'){
            //接收留言内容并写入数据表 
            $target = $input['target'];
            $cid = $input['cid'];
            $timelimit = $input['timelimit'];
            $deadline = Carbon::now()->addDays($timelimit*15);
            $title = $input['title'];
            $content = $input['content'];
            $initiate_id = auth('api')->user()->id;
            $initiate = auth('api')->user()->username;
            $entryTitle = $entry->title;
            // return $target;
            if($title && $content && $target == $level+1){
                // 创建评审计划
                $result = EntryReview::reviewCreate($id,$target,$cid,$deadline,$title,$content,$initiate_id,$initiate,$entryTitle);
                //发表了有效的讨论后，积分和成长值+100
                $result1 = User::expAndGrowValue($initiate_id,100,100);
                Entry::where('id',$id)->update(['review_id' => $result->id]);
            }
        }
        return ['success'=>$result? true:false];
    }
}
