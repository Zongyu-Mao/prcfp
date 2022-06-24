<?php

namespace App\Http\Controllers\Api\Organization\GroupDoc;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupDoc;
use App\Home\Organization\Group\GroupDoc\GroupDocComment;
use App\Home\Classification;
use Illuminate\Support\Facades\Auth;

class GroupDocController extends Controller
{
    //组织文档页
    public function groupDoc(Request $request){
    	$id = $request->id;
    	$title = $request->title;
    	$data = Group::where('id',$id)->with('groupEmblem')->with('classification')->first();
		$cid = $data->cid;
		$docs = $data->groupDocs;
		$user = auth('api')->user()->id;
 		$data_class = Classification::getClassPath($cid);
     	return array(
     		'class_path'	=> $data_class,
     		'group'	=> $data,
     		'docRecommend'	=> $docs->first(),
     		'docs'	=> $docs,
     	);
    }

    //组织文档详情页
    public function detail(Request $request){
    	$did = $request->id;
    	$title = $request->title;
    	$data = GroupDoc::find($did);
    	$group = Group::find($data->gid);
    	$data_class = Classification::getClassPath($group->cid);
    	$comments = GroupDocComment::where([['did',$did],['pid',0]])->with('allComment')->orderBy('created_at','desc')->get();
    	return array(
    		'doc'	=> $data,
    		'group'	=> $group,
    		'class_path'	=> $data_class,
    		'comments'	=> $comments
    	);
    }

    // 更改文档正文
    public function modify(Request $request){
        $data = $request->data;
        $id = $data['id'];
        $result = false;
        if(auth('api')->user()->id === GroupDoc::find($id)->creator_id){
            $content = $data['content'];
            $result = GroupDoc::modify($id,$content);
        }
        // 目前只需要return result就可以了，前段直接替换内容
        return array(
            'success'   => $result?true:false
        );
    }
}
