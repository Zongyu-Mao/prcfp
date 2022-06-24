<?php

namespace App\Http\Controllers\Api\Picture;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Classification;
use App\Models\Picture\Picture;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Picture\PictureTemperature;
use App\Models\Picture\PictureTemperatureRecord;
use Illuminate\Support\Facades\Redis;

class PictureController extends Controller
{
    //
    public function pictureIndex(Request $request) {
        // picture是根据用户的主专业得到的
        $pictures = [];
        
        $user = auth('api')->user();
        if(!count(Picture::all()))return $res = ['pictures' => $pictures,'speciality' => $user->getSpecialty];

        $sc = $user->specialty;
        $hasSc = $sc?true:false;
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $nextmonth = $today->addMonth();
        $lastmonth = $today->subMonth();
        $interest_pictures = [];
        // 主专业会有排行和最新图片两个，其余兴趣专业仅提供排行（在非首页也提供最新）
        if($sc) {
            $sc_pictures = Picture::whereIn('id',(Redis::ZREVRANGE('picture:classification:temperature:rank:'.$sc,0,4)))->with('class')->get();
            $sc_today_pictures = Picture::where('showtime','<',$tomorrow)->where('showtime','>',$today)->where('cid',$sc)->with('class')->get();
            $sc_new_pictures = Picture::where('cid',$sc)->with('class')->orderBy('created_at','desc')->limit(5)->get();
        }
        // 得到用户主专业和兴趣专业，如果没有主专业，选取兴趣专业，如果都没有，展示系统提供的专业
        $interests = $user->getInterest->pluck('id')->toArray();
        if(count($interests)){
            foreach($interests as $in) {
                $cs = Redis::ZREVRANGE('picture:classification:temperature:rank:'.$in,0,4);
                if(count($cs)) {
                   array_push($interest_pictures, Picture::whereIn('id',$cs)->with('class')->get()); 
                }
            }
        }
        $allRanks = Picture::whereIn('id',(Redis::ZREVRANGE('picture:temperature:rank',0,9)))->with('class')->get();

        return $res = [
            'allRanks' => $allRanks,
            'sc_pictures' => $sc_pictures,
            'sc_new_pictures' => $sc_new_pictures,
            'sc_today_pictures' => $sc_today_pictures,
            'interest_pictures' => $interest_pictures,
            'speciality' => $user->getSpecialty,
        ];
    }

    // 具体专业下
    public function picturesUnderclass(Request $request) {
        $data = $request->data;
        $cid = $data['id']; //此处id是class的id了
        $classname = $data['classname'];
        $pictures = [];
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $class = Classification::find($cid);
        $sc_pictures = Picture::whereIn('id',(Redis::ZREVRANGE('picture:classification:temperature:rank:'.$cid,0,4)))->with('class')->get();
        $sc_today_pictures = Picture::where('showtime','<',$tomorrow)->where('showtime','>',$today)->where('cid',$cid)->with('class')->get();
        $sc_new_pictures = Picture::where('cid',$cid)->with('class')->orderBy('created_at','desc')->limit(5)->get();
        return $res = [
            'class' => $class,
            'sc_pictures' => $sc_pictures,
            'sc_new_pictures' => $sc_new_pictures,
            'sc_today_pictures' => $sc_today_pictures,
        ];
    }

    // 具体图片
    public function feturedPictureDetail(Request $request) {
        $data = $request->data;
        $id = $data['id']; //此处id是class的id了
        $title = $data['title'];
        $picture = Picture::where('id',$id)->with('class')->with('links')->first();
        if(!$title==$picture->title)$picture='';
        $createtime = Carbon::now();
        $b_id = 85;
        PictureTemperatureRecord::recordAdd($id,Auth::user()->id,$b_id,$createtime);
        $tem = Redis::get('picture:temperature:'.$id);
        return $res = [
            'picture' => $picture,
            'tem' => $tem,
        ];
    }
}
