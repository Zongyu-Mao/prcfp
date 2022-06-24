<?php

namespace App\Http\Controllers\Api\Debate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\EntryDebate;
use Carbon\Carbon;

class DebateExpirationController extends Controller
{
    //复核攻辩的过期问题
    public function debate_expiration(Request $request) {
    	$id = $request->debate_id;
    	$debate = EntryDebate::find($id);
    	$checkTime = Carbon::now();
    	if($checkTime > $debate->deadline){
            $newDebate = EntryDebate::find($id);
            $check = '2';
            // 由于辩方原因超时，状态变更为3；
            $status = '1';
            $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算！';
            $Adata = $newDebate->ARedStars - $newDebate->ABlackStars;
            $Bdata = $newDebate->BRedStars - $newDebate->BBlackStars;
            if($Adata >= $Bdata){
                $victory = '1';
                $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。攻方['.$newDebate->Aauthor.']胜出！';
            }else{
                $victory = '2';
                $remark = '由于裁判['.$debate->referee.']未及时结算，系统已经自动结算。辩方['.$newDebate->Bauthor.']胜出！';
            }
            EntryDebate::debateTimeOutByRefereeClear($id,$status,$remark,$victory);
        }
    }
}
