<?php

namespace App\Http\Controllers\Api\Picture\PictureEdit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Picture\Picture;
use App\Home\Encyclopedia\Entry;
use Storage;
use App\Models\Picture\PictureTemperature;
use App\Models\Picture\PictureTemperatureRecord;
use App\Models\Picture\PictureEntryLink;
use Illuminate\Support\Facades\Redis;

class PictureEditController extends Controller
{
    public function featuredPicturesEditIndex(Request $request) {
    	$user = auth('api')->user();
    	$sc = $user->specialty;
    	$pictures = [];
    	$speciality = $user->getSpecialty??'';
    	$today = Carbon::today();
        
    	$tomorrow = Carbon::tomorrow();
        $nextmonth = Carbon::today()->addMonth();
        $lastmonth = Carbon::today()->subMonth();
        // return Carbon::parse($nextmonth);
       if(Picture::where('cid',$sc)->exists()) $pictures = Picture::where([['showtime','<',$nextmonth],['showtime','>',$today],['cid',$sc]])->orderBy('ups','desc')->get();
    	return $res = [
    		'pictures' => $pictures,
    		'today' => $today,
    		'tomorrow' => $tomorrow,
    		'nextmonth' => $nextmonth,
    		'lastmonth' => $lastmonth,
    		'speciality' => $speciality
    	];
    }


    // 上传图片（上传和编辑）
    public function featuredPictureEdit(Request $request) {
        // return $request;
        $user = auth('api')->user();
        $sc = $user->specialty;
        
        $modify = $request->modify;
        // return ['bac'=>$modify];
        $cid = $request->cid ?? 0;
        $filename = '';
        $path = '';
        $picture = '';
        $picture_id = 0;
        if ($request -> hasFile('file') && $request -> file('file') -> isvalid()) { //判断文件上传是否有效

            $filename = sha1(time() . $request -> file('file') -> getClientOriginalName()) . '.' . $request -> file('file') -> getClientOriginalExtension();
            
            $result = false;
            
            // return ['bac'=>$newshowtime,'abc'=>$showtime,];
            if($sc==$cid && $modify==2){
                
                $showtime = $request->showtime;
                $newshowtime = Carbon::createFromTimestampMs($showtime,'PRC')->toDateString();
                // 非编辑，新建模式，直接存储文件并新建特色记录，给前台直接跳到说明编辑页面。这样可以防止反复上传图片导致多存
                $path = $request -> file('file')->storeAs(
                    'featuredPictures/'.$newshowtime,$filename, 'public'
                );
                // return ['path'=>$path];
                // if($delpath = $user->getAvatar->url){
                //     Storage::disk('public')->delete($delpath);
                // }
                $title = 'title';
                $introduction = 'introduction';
                $url = $path;
                $creator_id = $user->id;
                $creator = $user->username;
                $result = Picture::newPicture($newshowtime,$cid,$title,$introduction,$url,$creator_id,$creator);
                $picture_id = $result;
            } elseif($modify==5) {
                // 更换了图片
                $picture_id = $id = $request->id;
                $picture = Picture::find($id)->only('id','url','showtime');
                $title= '';
                $introduction= '';
                $path = $request -> file('file')->storeAs(
                    'featuredPictures/'.$picture['showtime'],$filename, 'public'
                );
                $del = $picture['url'];
                if(Storage::disk('public')->delete($del))$result=Picture::pictureModify($id,$modify,$title,$introduction,$path);
            }
            if(!$result){
                Storage::disk('public')->delete($path);
                $path = '';
            }
            $b_id = 85;
            $createtime = Carbon::now();
            PictureTemperatureRecord::recordAdd($picture_id,$user->id,$b_id,$createtime);
            if($result) {
                $picture = Picture::where('id',$picture_id)->with('class')->with('links')->first();
            }
        }
        return $res = [
            'success' => $result?true:false,
            'id' => $result,
            'picture' => $picture,
            'path' => $path,
        ];
    }
    public function featuredPictureIntroductionEdit(Request $request) {
        // return $request;
        $data = $request->data;
        $user = auth('api')->user();
        $introduction = $data['introduction'];
        $title = $data['title'];
        $sc = $user->specialty;
        $id = $data['id'];
        $cid = $data['cid'];
        $url='';
        $picture = '';
        $modify = $data['modify'];//1是直接从新建页面过来的标题简介内容，2是更改了图片，3是更改title，4是更改introduction
        $result = Picture::pictureModify($id,$modify,$title,$introduction,$url);
        $b_id = 84;
        $createtime = Carbon::now();
        PictureTemperatureRecord::recordAdd($id,$user->id,$b_id,$createtime);
        // 由于这里title更改会重定向，而intro更改不需要回传picture的内容，因此此处只要回success
        return $res = [
            'success' => $result?true:false,
        ];
    }

    // 第三方链接词条
    public function entryLinkThird(Request $request) {
        // return $request;
        $data = $request->data;
        $user = auth('api')->user();
        $title = $data['title'];
        $sc = $user->specialty;
        $id = $data['id'];
        $modify = $data['modify'];//6是链接词条
        $result = false;
        $eid = 0;
        $picture = '';
        if($modify==6) {
            $eid = $data['eid'];
            $e_title = Entry::find($eid)->title;
            $createtime = Carbon::now();
            if($e_title==$title){
                $result = PictureEntryLink::link($id,$eid,$user->id,$createtime);
                $picture = Picture::where('id',$id)->with('class')->with('links')->first();
            }
        }
        return $res = [
            'success' => $result?true:false,
            'picture' => $picture,
            'tem' => Redis::get('picture:temperature:'.$id),
        ];
    }
}
