<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Encyclopedia\EntryReview;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryDebate;
use App\Home\Encyclopedia\EntryResort;
use App\Home\Publication\ArticleDebate;
use App\Home\Encyclopedia\Recommend\EntryRecommendation;
use App\Home\Encyclopedia\Recommend\EntryTemperature;
use App\Home\Examination\Recommend\ExamRecommendation;
use App\Home\Examination\Recommend\ExamTemperature;
use App\Home\Publication\Recommend\ArticleRecommendation;
use App\Home\Publication\Recommend\ArticleTemperature;
use App\Home\Announcement;
use App\Models\User;
use App\Home\Publication\ArticleResort;
use App\Home\Examination\ExamResort;
use App\Models\Picture\Picture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class HomeRecommendController extends Controller
{
    //
    public function getHomeRecommends(){
    	// $user = Auth::user();
    	// 首页的推荐内容,由于涉及专业性，因此推荐求助、攻辩等需要在主专业下
        // Redis::del('entry:temperature:rank');
        // Redis::del('article:temperature:rank');
        // Redis::del('exam:temperature:rank');
        $user = Auth::user();
    	$user_id = $user->id;
        $nosc = false;//默认有sc_id
        // 主专业
        $sc_id = $user->specialty;
        // xingqu
        $interest = $user->getInterest->pluck('id')->toArray();
        if($sc_id==0 && count($interest)>0){
            $sc_id=$interest[0];
        } else {
            $nosc = !$nosc;
            $sc_id = 38;//注意这里专业排行还没有 classificationTRank
        }

        if($sc_id) {
            // 本分类下的内容排行取值
            $e = Entry::where('id',(Redis::ZREVRANGE('entry:classification:temperature:rank:'.$sc_id,0,1)[0]??0))->with('entryAvatar')->with('classification')->first();

            $es = Entry::whereIn('id',Redis::ZREVRANGE('entry:classification:temperature:rank:'.$sc_id,1,3))->get(['id','title']);
            $a = Article::where('id',(Redis::ZREVRANGE('article:classification:temperature:rank:'.$sc_id,0,1)[0]??0))->with('articleAvatar')->with('classification')->first();

            $as = Article::whereIn('id',Redis::ZREVRANGE('article:classification:temperature:rank:'.$sc_id,1,3))->get(['id','title']);
            $exam = Exam::where('id',(Redis::ZREVRANGE('exam:classification:temperature:rank:'.$sc_id,0,1)[0]??0))->with('examAvatar')->with('classification')->first();
            $exams = Exam::whereIn('id',Redis::ZREVRANGE('exam:classification:temperature:rank:'.$sc_id,1,3))->get(['id','title']);
            $resorts_e = EntryResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(3)->get();
            $resorts_a = ArticleResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(3)->get();
            $resorts_exam = ExamResort::where([['cid',$sc_id],['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->limit(3)->get();
            $resorts = $resorts_e->merge($resorts_a)->merge($resorts_exam);
            if(Picture::where('cid',$sc_id)->exists()){
                // $pictures=Picture::where([['showtime','<',$tomorrow],['showtime','>',$today],['cid'],$sc])->get();
                $picture=Picture::where([['showtime','<',Carbon::tomorrow()],['showtime','>',Carbon::today()],['cid',$sc_id]])->with('class')->orderBy('ups','desc')->first();
                if(!$picture) $picture = Picture::where('cid',$sc_id)->with('class')->orderBy('ups','desc')->first();
            }
            if(!$picture)$picture = Picture::orderBy('ups','desc')->with('class')->first();
        }else {
            // 总榜下取值,如果限定了scid，目前这个就没有用了
            $e = Entry::where('id',(Redis::ZREVRANGE('entry:temperature:rank',0,1)[0]))->with('entryAvatar')->with('classification')->first();
            $es = Entry::whereIn('id',Redis::ZREVRANGE('entry:temperature:rank',1,3))->get(['id','title']);
            $a = Article::where('id',(Redis::ZREVRANGE('article:temperature:rank',0,1)[0]))->with('articleAvatar')->with('classification')->first();
            $as = Article::whereIn('id',Redis::ZREVRANGE('article:temperature:rank',1,3))->get(['id','title']);
            $exam = Exam::where('id',(Redis::ZREVRANGE('exam:temperature:rank',0,1)[0]))->with('examAvatar')->with('classification')->first();
            $exams = Exam::whereIn('id',Redis::ZREVRANGE('exam:temperature:rank',1,3))->get(['id','title']);
            $resorts_e = EntryResort::where([['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->with('classification')->limit(3)->get();
            $resorts_a = ArticleResort::where([['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->with('classification')->limit(3)->get();
            $resorts_exam = ExamResort::where([['pid',0],['status',0]])->orderBy('created_at','desc')->with('getContent')->with('classification')->limit(3)->get();
            $resorts = $resorts_e->merge($resorts_a)->merge($resorts_exam);
        }
        $interestArr = count($interest)>10?array_rand($interest,10):$interest;
        $incs_e = [];
        $incs_a = [];
        foreach($interestArr as $in) {
            if(Entry::where('cid',$in)->exists())array_push($incs_e,Entry::where('id',(Redis::ZREVRANGE('entry:classification:temperature:rank:'.$in,0,1)[0]))->with('entryAvatar')->with('classification')->first());
            if(Article::where('cid',$in)->exists())array_push($incs_a,Article::where('id',(Redis::ZREVRANGE('article:classification:temperature:rank:'.$in,0,1)[0]))->with('articleAvatar')->with('classification')->first());
        }
        $debates_e = EntryDebate::orderBy('created_at','desc')->with('getContent')->limit(3)->get();
        $debates_a = ArticleDebate::orderBy('created_at','desc')->with('getContent')->limit(3)->get();
        $debates = $debates_e->merge($debates_a);

        $my_es = Entry::where('manage_id',$user_id)->orderBy('updated_at','desc')->limit(3)->get(['id','title','updated_at']);
        $my_as = Article::where('manage_id',$user_id)->orderBy('updated_at','desc')->limit(3)->get(['id','title','updated_at']);
        $my_exs = Exam::where('manage_id',$user_id)->orderBy('updated_at','desc')->limit(3)->get(['id','title','updated_at']);
        // $my = $my_es->merge($my_as->merge($my_exs));
        $my = [
            'es'=>  $my_es,
            'as'=>  $my_as,
            'exs'=>  $my_exs,
        ];
        // redis中找到热度词条
        // $entry_redis = Redis::ZREVRANGE('entry:temperature:rank',0,999);
        // // 处理当中的eid和cid的关系
        // $entry_ranks = array();
        // foreach($entry_redis as $rank){
        //     $rank = explode(':',$rank);
        //     if(in_array($rank[1], $interest)){
        //         array_push($entry_ranks, $rank[0]);
        //     }
        // }
        // // 只取排行榜的前40条数据
        // if(count($entry_ranks)>40){
        //     $entry_ranks = array_slice($entry_ranks,40);
        // }
        // // 还没想好怎么加入缓存，因此这里直接读取数据库先
        // $entries = Entry::whereIn('id',$entry_ranks)->with('entryAvatar')
        //     ->with('classification')->get();
        // // 这里让数据根据排行榜顺序显示，use的作用是将外部变量传递入闭包
        // $entries = $entries->sortBy(function($item) use($entry_ranks){
        //     return array_search($item->id,$entry_ranks);
        // });

        
        // 得到热度最高的推荐
        // $rec_entries = EntryTemperature::whereIn('eid',$entry_rec_ids)->with('getEntry')->orderBy('temperature','asc')->get();

        
        
        // dd($entry_hot);
        // 获取公告信息
        $announcements = Announcement::limit(20)->orderBy('createtime','desc')->get();
        $hot = array(
            'entry' => $e,
            'article' => $a,
            'exam' => $exam,
            'es' => $es,
            'as' => $as,
            'exams' => $exams,
            'sc_id' => $user->specialty,
            'specialty' => $user->getSpecialty,
            'in_id' => count($interest)?$interest[0]:0,
            'user' => $user,
            'my' => $my,
            // 'v' => JWTAuth::factory()->getTTL(),
            'w' => env('SESSION_LIFETIME'),
            'ina' => $interestArr,
            'ccc' => $sc_id,
            'incs_e' => $incs_e,
            'incs_a' => $incs_a,
            'debates' => $debates,
            'resorts' => $resorts??'',
            'picture' => $picture??'',
            'extra' => auth('api')->user(),
        );


        return json_encode($hot);
    }
}
