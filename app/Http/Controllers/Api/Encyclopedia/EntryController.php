<?php

namespace App\Http\Controllers\Api\Encyclopedia;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Keyword;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Encyclopedia\Entry\EntryPicture;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\Recommend\EntryTemperatureRecord;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App\Models\Committee\Surveillance\SurveillanceRecord;
use App\Models\Committee\Surveillance\SurveillanceMark;
use App\Models\Committee\Surveillance\SurveillanceWarning;
use App\Models\Encyclopedia\Ambiguity\Synonym;
use App\Models\Encyclopedia\Ambiguity\Polysemant;
use App\Home\Personnel\Behavior;
use Carbon\Carbon;

class EntryController extends Controller
{
    //
    public function getEntry(Request $request,$id,$title){
        $data_req = Entry::find($id);
        $synonyms = [];
        $polysemants = [];
        // return $request;
        // 确认是否需要跳转,2是同义词主词,3是同义词，4是歧义词，4不需要跳转，5是同义词或歧义词，这个还没做
        if($data_req->status<5){
            $data = Entry::where('id',$id)->with('entryAvatar')->first();
            if(Polysemant::where('eid',$id)->orWhere('poly_id',$id)->exists()){
                $poly_ids1 = Polysemant::where('eid',$id)->pluck('poly_id')->toArray();
                $poly_ids2 = Polysemant::where('poly_id',$id)->pluck('eid')->toArray();
                $p_ids = array_unique(array_merge($poly_ids1,$poly_ids2));
                $polysemants = Entry::whereIn('id',$p_ids)->get();
            }
            if(Synonym::where('sid',$id)->exists()){
                $sids = Synonym::where('eid',$id)->pluck('sid')->toArray();
                $synonyms = Entry::whereIn('id',$sids)->get(['id','title','etitle']);
            }
        }else if($data_req->status==5){
            $eid = Synonym::where('sid',$id)->first()->eid;
            $sids = array_diff(Synonym::where('eid',$eid)->pluck('sid')->toArray(),[$id]);
            if($id!=$eid)array_push($sids,$eid);
            $data = Entry::where('id',$eid)->with('entryAvatar')->first();
            $synonyms = Entry::whereIn('id',$sids)->get(['id','title','etitle']);
        }
        // $cooperation = $data->entryCooperation;     
     	if($id && $data->title){
     		$references = $data->entryReference()->get();
     		$entryContents = EntryContent::where('eid',$data->id)->orderBy('sort','asc')->get()??'';

            Redis::INCR('entry:views:'.$data->id);
            Redis::INCR('entry:temperature:'.$data->id);
            $behavior = Behavior::find(54);
            // Redis::INCRBY('entry:temperature:'.$data->id,$behavior->score);
            // 更新排行榜热度,总榜
            Redis::ZINCRBY('entry:temperature:rank',1,$data->id);
            // 分类榜
            Redis::ZINCRBY('entry:classification:temperature:rank:'.$data->cid,1,$data->id);
            // 分类顺序榜
            Redis::ZINCRBY('classification:temperature:rank',1,$data->cid);
            // 此处热度是在Redis下，没有在Cache下
     		$temperature = Redis::GET('entry:temperature:'.$data->id);

            // dd($temperature);
            $cid = $data->cid;
            $user = auth('api')->user();
            $role = $user->getRole;
            $committee = $user->getCommittee;
            $user_id = $user->id??0;
            $data_class = Classification::getClassPath($cid);
            $focus = $data->entryFocus()->find($user_id) ? true : false;
            $collect = $data->entryCollect()->find($user_id) ? true : false;
            $keywords = $data->keywords()->get();
            $cooperation = EntryCooperation::find($data->cooperation_id);
            $crewArr = $cooperation?$cooperation->crews()->pluck('user_id')->toArray():[];
            $data->manage_id?array_push($crewArr,$data->manage_id):'';
     			
            $ex_entries = $data->extendedEntryReadings()->get();
            $ex_articles = $data->extendedArticleReadings()->get();
     		$ex_exams = $data->extendedExamReadings()->get();
            $time = Carbon::now()->addMonth();
            // 巡查根据status判断，标记根据时间判断
            $surveillances = SurveillanceRecord::where([['sid',$id],['status','<',2]])->get()??[];
            $marks = SurveillanceMark::where([['sid',$id],['updated_at','>',$time]])->get()??[];
     		$warnings = SurveillanceWarning::where([['sid',$id],['status','<',2]])->get()??[];
     		
            $behavior_id = 1;
            // 是否推荐
            // 取到是否推荐过  可以考虑再取推荐次数
            $rec_check = $user_id?Redis::SISMEMBER('entry:recommend:userid:'.$user_id,$id):0;

     		$return = [
     			'entry' => $data,
                'class' => $data_class,
                'crewArr' => $crewArr,
     			'contents' => $entryContents,
     			'ex_entries' => $ex_entries,
                'ex_articles' => $ex_articles,
     			'ex_exams' => $ex_exams,
                'focus' => $focus,
     			'collect' => $collect,
     			'keywords' => $keywords,
     			'rec_check' => $rec_check,
                'references' => $references,
                'surveillances' => $surveillances,
                'marks' => $marks,
     			'warnings' => $warnings,
                'user'=>$user,
                'role'=>$role,
                'committee'=>$committee,
                'synonyms'=>$synonyms,
                'polysemants'=>$polysemants,
                'data_req'=>$data_req->only('status'),
                'rank'=>Redis::ZREVRANGE('entry:temperature:rank',0,999),
                'tem'=>$temperature,
                'b'=>$behavior
     		];
     		return $return;
     	}
    }
}
