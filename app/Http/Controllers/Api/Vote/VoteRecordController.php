<?php

namespace App\Http\Controllers\Api\Vote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\Vote;
use Carbon\Carbon;

class VoteRecordController extends Controller
{
    //
    public function voting(Request $request) {
        $data = $request->data;
        $user_id = $data['user_id'];
        $vote = Vote::find($data['vid']);
        $result = 0;
        $my = '';
        if($vote->type==$data['type']){
            $createtime = Carbon::now();
            if($data['type']==1 && $data['singleVoteChoice']){
                VoteRecord::recordCreate($data['vid'],$user_id,$data['singleVoteChoice'],$createtime);
                $result++;
            }elseif($data['type']==2 && $data['voteArr']){
                $voteArr = $data['voteArr'];
                foreach($voteArr as $vote) {
                    VoteRecord::recordCreate($data['vid'],$user_id,$vote,$createtime);
                    $result++;
                }
            }
        }

        $optArr = $vote->voteOptions->pluck('id')->toArray();
        $recordArr = [];
        foreach($optArr as $opt) {
            $count = VoteRecord::where([['vid',$data['vid']],['choice',$opt]])->count();
            $recordArr['opt_'.$opt]=$count;
        }


        $s = Vote::find($data['vid'])->status;//正常来说vote不会在投票期间改变状态，这里先设上
        $my = VoteRecord::where([['vid',$data['vid']],['user_id',$user_id]])->get();
    	return [
            'success'=>$result ? true: false,
            'amount'=>$result,
            'my'=>$my,
            'status'=>$s,
            'records' => $recordArr
        ];
    }
}
