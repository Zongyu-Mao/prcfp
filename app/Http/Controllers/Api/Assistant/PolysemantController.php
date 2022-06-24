<?php

namespace App\Http\Controllers\Api\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Encyclopedia\Ambiguity\Polysemant;
use App\Models\Encyclopedia\Ambiguity\Synonym;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;

class PolysemantController extends Controller
{
    // polysemant: 多义的,是同标题不同内容，status均未1或0
    public function polysemantModify(Request $request) {
    	// 多义与好友类似 是双向的，但是数据库仅记录一次
    	$data = $request->data;
        $result = '';
        if($data['clear']) {
            $result = Polysemant::clearPolysemant($data['id'],$data['obj_id']);
        }
        if($result) {
            $entry = Entry::find($data['id']);
            $poly_ids1 = Polysemant::where('eid',$data['id'])->pluck('poly_id')->toArray();
            $poly_ids2 = Polysemant::where('poly_id',$data['id'])->pluck('eid')->toArray();
            $p_ids = array_unique(array_merge($poly_ids1,$poly_ids2));
            $basic = Entry::where('id',$data['id'])->with('entryAvatar')->first();
            $backContent = Entry::whereIn('id',$p_ids)->get(); //$polysemants
        }
        return $res = [
            'success'=>$result?true:false,
            'basic'=>Entry::where('id',$data['id'])->with('entryAvatar')->first(),
            'backContent'=>$backContent,
        ];
    }

    // 如果新建内容和已有内容主标题相同，直接返回该内容主副标题和id
    public function create_polysemant_check(Request $request) {
    	$data = $request->data;//相同标题内容在所有主块都要确认并返回，但是如果是词条的话可以定义成歧义词，其他板块目前原则上不允许相同标题
    	$title = $data['title'];
    	$scope = $data['scope'];
    	$check = false;
    	$basic = '';
    	if($scope==1) {
    		$check = Entry::where('title',$title)->exists();
            // status为5的同义词副词是不参与比较
    		if($check)$basic = Entry::where([['title',$title],['status','<=','1']])->get(['id','title','cid','etitle','status']);
    	} else if($scope==2) {
    		$check = Article::where('title',$title)->exists();
    		if($check)$basic = Article::where('title',$title)->get(['id','title','cid','etitle','status']);
    	} else if($scope==3) {
    		$check = Exam::where('title',$title)->exists();
    		if($check)$basic = Exam::where('title',$title)->get(['id','title','cid','etitle','status']);
    	}
    	return ['check'=>$check, 'basic'=>$basic];
    }
}
