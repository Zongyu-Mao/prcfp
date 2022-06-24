<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Examination\Exam;
use App\Home\Examination\ExamReview;
use App\Home\Examination\ExamResort;
use App\Home\Examination\ExamDebate;
use App\Home\Examination\ExamDiscussion\ExamOpponent;
use App\Home\Examination\Recommend\ExamRecommendation;
use App\Home\Examination\Recommend\ExamTemperature;
use App\Home\Classification;
use App\Home\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ExaminationController extends Controller
{
    //展示首页
    public function getExaminationRecommends(){
        // 百科首页数据主要包括：1是推荐词条（四级及以上）2是最新词条3是求助文章（未解决按时间和解决数，优先时间，有答案的按答案由少到多；已解决的按时间倒序
        // 最新词条按照时间顺序显示（修改时间）
        // 优先显示用户兴趣项
        $user = auth('api')->user();
        $id = $user->id;
        $interest=$user->getInterest->pluck('id')->toArray();
        $nosc = false;
        // 主专业
        $sc_id = $user->specialty;
        if($sc_id==0 && count($interest)>0){
            $sc_id=$interest[0];
        } else {
            $nosc = !$nosc;
            $sc_id = 38;//注意这里专业排行还没有 classificationTRank
        }
        if($sc_id) {
            // 本分类下的内容排行取值
            $rec = Exam::where('id',(Redis::ZREVRANGE('exam:classification:temperature:rank:'.$sc_id,0,1)[0]))->with('examAvatar')->with('classification')->first();
            $exs = Exam::whereIn('id',Redis::ZREVRANGE('exam:classification:temperature:rank:'.$sc_id,4,6))->get(['id','title']);
            $resorts = ExamResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(10)->get();
            // 百科首页的协作评选求助讨论辩论(协作因为与主内容绑定，因此不在此列)
            $reviews = ExamReview::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();

            $debates = ExamDebate::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();
        }
        $announcements = Announcement::where('scope','3')->orderBy('createtime','desc')->limit('10')->get();

        $incs_exam = [];
        if($interest) {
            foreach($interest as $in) {
                if(Exam::where('cid',$in)->exists())array_push($incs_exam,Exam::where('id',(Redis::ZREVRANGE('exam:classification:temperature:rank:'.$in,0,1)[0]))->with('examAvatar')->with('classification')->first());
            }
            $examNew = Exam::whereIn('cid',$interest)->orderBy('created_at','desc')->with('classification')->limit(20)->get();
        } else {
            $examNew = Exam::orderBy('created_at','desc')->with('classification')->limit(20)->get();
        }
        $my = Exam::where('manage_id',$id)->orderBy('updated_at','desc')->limit(6)->get(['id','title','updated_at']);
        $data = [
            'sc_id' => $user->specialty,
            'specialty' => $user->getSpecialty,
            'in_id' => count($interest)?$interest[0]:0,
        	'recommend' 	=> $rec,
            'exams'         => $examNew,
            'exs'         => $exs,
        	'interests' 		=> $incs_exam,
            'debates' => $debates,
            'reviews' => $reviews,
            'resorts' => $resorts,
            'my' => $my,
        	'announcements'	=> $announcements,
        ];
    	return json_encode($data);
    }
}
