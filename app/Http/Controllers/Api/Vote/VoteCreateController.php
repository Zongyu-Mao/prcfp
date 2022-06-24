<?php

namespace App\Http\Controllers\Api\Vote;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vote\Vote;
use App\Models\Vote\VoteRecord;
use App\Models\Vote\VoteOption;
use App\Models\Vote\VoteEvent;
use Carbon\Carbon;

class VoteCreateController extends Controller
{
    public function voteCreate(Request $request) {
    	$data = $request->data;
        $title = $data['title'];
        $content = $data['content'];
        $affiliation = $data['affiliation'];  
        $type = $data['type'];
        $amount = $data['amount'];
        $choice_limit = $data['choice_limit'];
        $timelimit = $data['timelimit'];
        $user_id = $data['user_id'];
        $user = auth('api')->user();
        $deadline = Carbon::now()->addMonths($timelimit);
        $result = 0;
        if($title && $content && $amount){
            // 处理一下options
            $remark = 'vote created done!';
            $result = Vote::newVote($affiliation,$type,$amount,$choice_limit,$user_id,$deadline,$title,$content,$remark);
            $options = $data['options'];;
            foreach($options as $option) {
                VoteOption::optionCreate($result,$option,Carbon::now());
            }
            VoteEvent::voteEventAdd($result,$user->id,$user->username,'创建了投票《'.$title.'》。',Carbon::now());
        }

        return [
            'success'=>$result ? true : false,
            'id' =>$result,
            'title' =>$title,
            'opt' =>$options
        ];
    }
}
