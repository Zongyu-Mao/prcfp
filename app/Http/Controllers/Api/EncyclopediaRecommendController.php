<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia;
use App\Home\Encyclopedia\Entry;
use App\Home\Classification;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryDiscussion\EntryOpponent;
use App\Home\Encyclopedia\Recommend\EntryRecommendation;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use Illuminate\Support\Facades\Redis;
use App\Home\Announcement;
use DB;
use Input;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EncyclopediaRecommendController extends Controller
{
    //
    public function getEncyclopediaRecommends() {
        $user = auth('api')->user();
    	$id = $user->id;
        // 主专业 由于涉及专业性，因此推荐求助、攻辩、反对、评审等需要在主专业下，而兴趣专业只给basic
        $sc_id = $user->specialty;
        // xingqu
        $interest = $user->getInterest->pluck('id')->toArray();
        $nosc = false;
        if($sc_id==0 && count($interest)>0){
            $sc_id=$interest[0];
        } else {
            $nosc = !$nosc;
            $sc_id = 38;//注意这里专业排行还没有 classificationTRank
        }
        if($sc_id) {
            // 本分类下的内容排行取值
            $e = Entry::where('id',(Redis::ZREVRANGE('entry:classification:temperature:rank:'.$sc_id,0,1)[0]))->with('entryAvatar')->with('classification')->first();
            $es = Entry::whereIn('id',Redis::ZREVRANGE('entry:classification:temperature:rank:'.$sc_id,4,6))->get(['id','title']);
            $resorts = EntryResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(10)->get();
            // 百科首页的协作评选求助讨论辩论(协作因为与主内容绑定，因此不在此列)
            $reviews = EntryReview::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();

            $debates = EntryDebate::where([['cid',$sc_id],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit('10')->get();
        }else {
            // 总榜下取值
            $e = Entry::where('id',(Redis::ZREVRANGE('entry:temperature:rank',0,1)[0]))->with('entryAvatar')->with('classification')->first();
            $es = Entry::whereIn('id',Redis::ZREVRANGE('entry:temperature:rank',4,6))->get(['id','title']);

        }
        $announcements = Announcement::where('scope',1)->orderBy('createtime','desc')->limit('10')->get();
        $interest = $user->getInterest->pluck('id')->toArray();
        $incs_e = [];
        if($interest) {
            foreach($interest as $in) {
                if(Entry::where('cid',$in)->exists())array_push($incs_e,Entry::where('id',(Redis::ZREVRANGE('entry:classification:temperature:rank:'.$in,0,1)[0]))->with('entryAvatar')->with('classification')->first());
            }
            $entryNew = Entry::whereIn('cid',$interest)->orderBy('created_at','desc')->with('classification')->limit(20)->get();
        } else {
            $entryNew = Entry::orderBy('created_at','desc')->with('classification')->limit(20)->get();
        }
        $my = Entry::where('manage_id',$id)->orderBy('updated_at','desc')->limit(6)->get(['id','title','updated_at']);
        
        
        // dd($debates);
        $data = [
            'sc_id' => $user->specialty,
            'specialty' => $user->getSpecialty,
            'in_id' => count($interest)?$interest[0]:0,
            'incs_e' => $incs_e,
            'recommend'     => $e,
        	'es' 	=> $es,
        	'entries' 		=> $entryNew,
            'debates' => $debates,
            'reviews' => $reviews,
            'resorts' => $resorts,
            'my' => $my,
        	'announcements'	=> $announcements,

        ];
    	return json_encode($data);
    }
}
