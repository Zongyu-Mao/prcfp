<?php

namespace App\Http\Controllers\Api\Globalization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Globalization\GlobalUserAdvise;
use App\Models\Globalization\GlobalUserAdvise\GlobalUserAdviseComment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAdviseController extends Controller
{
	public function global_advises(Request $request) {
		$data = $request->data;
        $pageSize = $data['pageSize'];
		$as = GlobalUserAdvise::orderBy('createtime','desc')->with('creator')->paginate($pageSize);
		return [
            'advises'=> $as,
        ];
	}

	public function global_advise(Request $request) {
		$data = $request->data;
        $id = $data['id'];
		$title = $data['title'];
		$a = GlobalUserAdvise::where('id',$id)->with('creator')->first();
        $cs = GlobalUserAdviseComment::where('advise_id',$id)->with('creator')->orderBy('createtime','desc')->get();
		return [
            'advise'=> $a,
            'comments'=> $cs
        ];
	}

    //	新的建议
    public function new_advise(Request $request) {
    	$data = $request->data;
        $title = $data['title'];
        $content = $data['content'];
        $scope = $data['scope_op'];
        $createtime = Carbon::now();
        $user_id = auth('api')->user()->id;
        $result = GlobalUserAdvise::newAdvise($user_id,$title,$content,$scope,$createtime);
        return [
    		'success'	=>	$result->id?true:false
    	];
	}

	//	新的评论
    public function new_comment(Request $request) {
		$data = $request->data;
        $content = $data['content'];
        $advise_id = $data['advise_id'];
        $createtime = Carbon::now();
        $user_id = auth('api')->user()->id;
        $createtime = Carbon::now();
        $result = GlobalUserAdviseComment::newComment($user_id,$advise_id,$content,$createtime);
        $cs = GlobalUserAdviseComment::where('advise_id',$advise_id)->with('creator')->orderBy('createtime','desc')->get();
        return [
    		'success'	=>	$result?true:false,
            'comments'=> $cs
    	];
	}

}
