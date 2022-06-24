<?php

namespace App\Http\Controllers\Api\Vote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vote\Vote;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteOption;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Home\Announcement;

class VoteController extends Controller
{
    //
    public function votes(Request $request) {
        $votes = Vote::where('status',0)->orderBy('created_at','desc')->limit(20)->get();
        $oldVotes = Vote::where('status','!=',0)->orderBy('created_at','desc')->limit(20)->get();
    	$announcements =  Announcement::where('scope',8)->orderBy('createtime','desc')->limit(10)->get();
        return array(
            'votes' => $votes,
            'oldVotes' => $oldVotes,
            'announcements' => $announcements
        );
    }

    public function getVote(Request $request) {
        $id = $request->id;
        $title=$request->title;
        
        $vote = Vote::where([['id',$id],['title',$title]])->with('voteOptions')->with('getAuthor')->first();
        $optArr = $vote->voteOptions->pluck('id')->toArray();
        $recordArr = [];
        foreach($optArr as $opt) {
            $count = VoteRecord::where([['vid',$id],['choice',$opt]])->count();
            $recordArr['opt_'.$opt]=$count;
        }
        $user_id = auth('api')->user()->id;
        $myRecord = VoteRecord::where([['vid',$id],['user_id',$user_id]])->get();
        return array(
            'vote' => $vote,
            'myRecord' => $myRecord,
            'records' => $recordArr
        );
    }
    // 结束投票
    public function voteFinish(Request $request) {
        $data = $request->data;
        $id = $data['id'];
        $user_id = $data['user_id'];
        $remark = $data['remark'];
        $result = 0;
        $status = 2;
        if($remark) {
            $result = Vote::voteFinish($id,$status,$remark);
        }
        return [
            'success'=>$result ? true: false
        ];
    }
}
