<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Classification;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Personnel\Inform;
use App\Home\Personnel\Inform\InformMedal;
use App\Home\Personnel\Medal;
use App\Home\Personnel\MedalSuit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BasicInformController extends Controller
{
    //主内容的举报
    //举报页面
    public function basicInform(Request $request){
    	// dd($scope);
    	$scope = $request->scope;
    	$obj_id = $request->id;
        $obj_title = $request->name;
    	$top_cid = $request->cid;
    	switch($scope)
    	{
    		case 1:
    		$data = Entry::find($obj_id);
    		$remark = '百科词条《'.$data->title.'》';
    		$url = '/encyclopedia/reading/'.$obj_id.'/'.$obj_title;
    		break;
    		case 2:
    		$data = Article::find($obj_id);
    		$remark = '著作《'.$data->title.'》';
    		$url = '/publication/reading/'.$obj_id.'/'.$obj_title;
    		break;
    		case 3:
    		$data = Exam::find($obj_id);
    		$remark = '试卷《'.$data->title.'》';
    		$url = '/examination/reading/'.$obj_id.'/'.$obj_title;
    		break;
    		case 4:
    		$data = Group::find($obj_id);
    		$remark = '组织《'.$data->title.'》';
    		$url = '/organization/group/'.$obj_id.'/'.$obj_title;
    		break;
            default: 
            break;
    	}

		$title = $request->title;
		$content = $request->content;
		$medals = $request->medalArr;
		$result = false;
		$belong = 1;
		$weight = 0;
		for($i=0;$i<count($medals);$i++){
			$weight += Medal::find($medals[$i])->weight;
		}
        $user = auth('api')->user();
		$author_id = $user->id;
		$object_user_id = $data->manage_id;
		// status需要重新计划************************************************************
		$weight<10 ? $status = 0 : $status = 3;
		$createtime = Carbon::now();
        // 这里要先扣去用户金币
        if($user->gold>=1 && $user->update(['gold'=>$user->gold-1])){
            $result = Inform::informAdd($author_id,$top_cid,$object_user_id,$title,$weight,$content,$url,$remark,$scope,$belong,$obj_id,$status);
        }
		for($i=0;$i<count($medals);$i++){
			InformMedal::create([
				'inform_id'	=> $result->id,
				'medal_id'	=> $medals[$i],
				'createtime'	=> $createtime
			]);
		}
    	return [
            'type'   => 1,
            'inform'   => $result,
    		'success'	=> $result? true:false
    	];
    }

    public function basicContentCheck(Request $request) {
        $input = $request->data;
        $scope = $input['scope'];
        $obj_id = $input['id'];
        $obj_title = $input['name'];
        $cid = $input['cid'];
        $classname = $input['classname'];
        $data = false;
        $cla = false;
        switch($scope)
        {
            case 1:
            $data = Entry::where([['id',$obj_id],['title',$obj_title]])->exists();
            $class_id = Entry::find($obj_id)->cid;
            break;
            case 2:
            $data = Article::where([['id',$obj_id],['title',$obj_title]])->exists();
            $class_id = Article::find($obj_id)->cid;
            break;
            case 3:
            $data = Exam::where([['id',$obj_id],['title',$obj_title]])->exists();
            $class_id = Exam::find($obj_id)->cid;
            break;
            case 4:
            $data = Group::where([['id',$obj_id],['title',$obj_title]])->exists(); 
            $class_id = Group::find($obj_id)->cid;
            break;
            default: 
            break;
        }
        if($data && $class_id) {
            $class3_id = Classification::find($class_id)->pid;
            $class2_id = Classification::find($class3_id)->pid;
            $t_class = Classification::find($class_id);
            if($t_class->id = $cid && $t_class->classname = $classname){
                $cla = true;
            }

        }
        return  [
            'check'   => ($data&&$cla) ? true:false
        ];
    }
}
