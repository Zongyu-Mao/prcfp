<?php

namespace App\Http\Controllers\Api\Personal\PrivateMedal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personal\PrivateMedal;
use App\Models\Personal\PrivateMedalRecord;
use Illuminate\Support\Facades\Auth;

class PrivateMedalController extends Controller
{
    //
    public function privateMedals(Request $request) {
    	$user = auth('api')->user();
    	$medals = PrivateMedal::where('creator_id',$user->id)->with('creator')->with('owner')->orderBy('created_at','desc')->get();
    	return ['medals'=>$medals];
    }

    public function privateMedalCreate(Request $request) {
        $user = auth('api')->user();
    	$data = $request->data;
    	$re_path = $data['path'];
    	$user = auth('api')->user();
        $result = 0;
    	if(count($re_path)>1){
            $pathD = $re_path;
            for($i=0;$i<count($pathD)-1;$i++){
                Storage::disk('public')->delete($pathD[$i]);
            }
        }
        $path = '/storage/' . end($re_path);
        if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = PrivateMedal::privateMedalAdd($data['title'],$path,$data['description'],$user->id);
        }else{
            Storage::disk('public')->delete(end($re_path));
        }
        // 尝试不要result判断
        $medals = PrivateMedal::where('creator_id',$user->id)->with('creator')->with('owner')->orderBy('created_at','desc')->get();
    	return ['success'=>$result?true:false,'medals'=>$medals];
    }

    public function medalGiving(Request $request) {
        $data = $request->data;
        $id = $data['medal_id'];
        $user_id = $data['user_id'];
        $owner_id = $data['owner_id'];
        $user = auth('api')->user();
        $status = 1;
        $result = PrivateMedal::medalGiving($id,$owner_id,$status);
        $medals = PrivateMedal::where('creator_id',$user->id)->with('creator')->with('owner')->orderBy('created_at','desc')->get();
        return ['success'=>$result?true:false,'medals'=>$medals];
    }
}
