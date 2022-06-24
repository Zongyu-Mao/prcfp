<?php

namespace App\Http\Controllers\Api\Picture\MindMap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Models\Encyclopedia\EntryHistory\EntryMindMap;
use App\Models\Publication\ArticleHistory\ArticleMindMap;
use App\Models\Examination\ExamHistory\ExamMindMap;
use Illuminate\Support\Facades\Auth;

class MindMapController extends Controller
{
    //得到脑图
    public function getMindMap(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$id = $data['id'];
    	$title = $data['title'];
    	$elements = [];
    	if($scope==1) {
    		if(Entry::where([['id',$id],['title',$title]])->exists()){
    			$elements = EntryMindMap::where([['oid',$id],['pid',0]])
	    			->with('basicContent:id,title,level')
	    			->with('allElements')
	    			->with('creator:id,username')
	    			->with('editor:id,username')
	    			->orderBy('created_at')
	    			->get();
    		}
    	}else if($scope==2) {
    		if(Article::where([['id',$id],['title',$title]])->exists()){
    			$elements = ArticleMindMap::where([['oid',$id],['pid',0]])
	    			->with('basicContent:id,title,level')
	    			->with('allElements')
	    			->with('creator:id,username')
	    			->with('editor:id,username')
	    			->orderBy('created_at')
	    			->get();
    		}
    	}else if($scope==3) {
    		if(Exam::where([['id',$id],['title',$title]])->exists()){
    			$elements = ExamMindMap::where([['oid',$id],['pid',0]])
	    			->with('basicContent:id,title,level')
	    			->with('allElements')
	    			->with('creator:id,username')
	    			->with('editor:id,username')
	    			->orderBy('created_at')
	    			->get();
    		}
    	}

    	return $res = [
    		'elements' => $elements,
    	];
    }

    public function modifyMindMap(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$isCreate = $data['isCreate'];
    	$result = false;
        $elements = '';
    	if($isCreate) {
    		$oid = $data['oid'];
    		$bid = $data['bid'];
    		$pid = $data['pid'];
    		$title = $data['title'];
    		$type = $data['type'];
    		$creator_id = $data['creator_id'];
    		if($scope==1){
    			$result = EntryMindMap::newMindMapRecord($pid,$oid,$bid,$title,$type,$creator_id);
                if($result) {
                    $elements = EntryMindMap::where([['oid',$oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		} else if ($scope==2){
    			$result = ArticleMindMap::newMindMapRecord($pid,$oid,$bid,$title,$type,$creator_id);
                if($result) {
                    $elements = ArticleMindMap::where([['oid',$oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		} else if ($scope==3){
    			$result = ExamMindMap::newMindMapRecord($pid,$oid,$bid,$title,$type,$creator_id);
                if($result) {
                    $elements = ExamMindMap::where([['oid',$oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		}
    	} else {
            $id = $data['id'];
    		$bid = $data['bid'];
    		$title = $data['title'];
    		$type = $data['type'];
    		$editor_id = $data['editor_id'];
    		if($scope==1){
    			$result = EntryMindMap::modifyMindMapRecord($id,$bid,$title,$type,$editor_id);
                if($result) {
                    $elements = EntryMindMap::where([['oid',EntryMindMap::find($id)->oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		} else if ($scope==2){
    			$result = ArticleMindMap::modifyMindMapRecord($id,$bid,$title,$type,$editor_id);
                if($result) {
                    $elements = ArticleMindMap::where([['oid',ArticleMindMap::find($id)->oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		} else if ($scope==3){
    			$result = ExamMindMap::modifyMindMapRecord($id,$bid,$title,$type,$editor_id);
                if($result) {
                    $elements = ExamMindMap::where([['oid',ExamMindMap::find($id)->oid],['pid',0]])
                    ->with('basicContent:id,title,level')
                    ->with('allElements')
                    ->with('creator:id,username')
                    ->with('editor:id,username')
                    ->orderBy('created_at')
                    ->get();
                }
    		}
    	}
    	return $re = [
    		'success' => $result ? true : false,'elements'=>$elements
    	];
    }

    public function elementContentCheck(Request $request) {
    	$data = $request->data;
    	$scope = $data['type'];
    	$id = $data['bid'];
    	$title = '';
    	if($id && $scope) {
    		switch($scope) {
    			case 1:
    				$basic = Entry::find($id);
    				break;
    			case 2:
    				$basic = Article::find($id);
    				break;
    			case 3:
    				$basic = Exam::find($id);
    				break;
    			default: 
    				break;
    		}
            if($basic) {
                $title = $basic->only('status','title');
            }
    	}
    	return $re = [
    		'id' => $id,
    		'title' => $title
    	];
    }
}
