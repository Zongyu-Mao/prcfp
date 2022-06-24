<?php

namespace App\Http\Controllers\Api\CommonShare;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryReference;
use App\Home\Publication\Article\Reference\ArticleReference;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReferenceController extends Controller
{
    //词条参考文献的添加
    public function referenceAdd(Request $request){
		$id = $request->id;
		$scope = $request->scope;
		$add = '';
     	if($id){
     		// dd($id);
     		$creator = Auth::user()->id;
     		//添加延伸阅读
 		 	$references = $request->reference;
 		 	$sort = $references['sort'];
 		 	if(count($references)==7 && $sort>0){
 		 		if($scope==1){
 		 			if(EntryReference::where([['entry_id',$id],['sort','>=',$sort]])->exists()) {
 		 				EntryReference::where([['entry_id',$id],['sort','>=',$sort]])->increment('sort');
 		 			}
		 		 $add = EntryReference::referenceAdd($id,$sort,$references['type'],$references['author'],$references['title'],$references['periodical'],$references['publish'],$references['pagenumber'],$creator,$creator);
	 		 	}elseif($scope==2){
	 		 		if(ArticleReference::where([['part_id',$id],['sort','>=',$sort]])->exists()) {
	 		 			ArticleReference::where([['part_id',$id],['sort','>=',$sort]])->increment('sort');
	 		 		}
	 		 		$add = ArticleReference::referenceAdd($id,$sort,$references['type'],$references['author'],$references['title'],$references['periodical'],$references['publish'],$references['pagenumber'],$creator,$creator);
		 		 }
 		 	}
 		 	
 		 	
     	}
     	$res = [];
     	if($add){
            if($scope===1){
                $res = Entry::find($id)->entryReference()->orderBy('sort','asc')->get();
            }else if ($scope===2) {
                $res = ArticleReference::where('part_id',$id)->orderBy('sort','asc')->get();
            }
        }
     	return ['success'=>$add?true:false,'references'=>$res];
     }

     //参考文献的修改
    public function referenceModify(Request $request){
    	$arr = [];
    	$sortSuc = 0;
    	$success = false;
    	$inputSort = '';
    	$res = [];
    	foreach ($request->reference as $key => $value) {
    		if($key != 'sort'){
    			$arr[$key] = $value;
    		}else{
    			$inputSort = $value;
    		}
    	}
    	// 如果有sort更改，需要单独处理
    	$id = $request->reference_id;
    	$scope = $request->scope;
    	$references = $request->reference;
    	$revisor = Auth::user()->id;
    	if($scope==1){
    		$data = EntryReference::find($id);
	 		$eid = $data['entry_id'];
	 		$sort = $data['sort'];
	 		
	 		if($inputSort){
	 			$referencesSort=EntryReference::where([['entry_id',$eid],['sort',$inputSort]])->first();
	 			if($referencesSort){
	 				// 如果有sort的更改，先找到要替换sort的对象，如果有，要先替换为当前参考文献的sort
	 				$sortModify = EntryReference::where('id',$referencesSort['id'])->update([
	 					'sort'=>$sort
	 				]);
		 		}
				// 然后再将当前参考文献的sort变更为input值
	 			$resultSort = EntryReference::where('id',$id)->update([
	 					'sort'=>$inputSort
	 				]);
	 			$sortSuc++;
	 			$success = true;
	 		}
	 		if(count($arr)){
	 			if(EntryReference::where('id',$id)->update($arr)){
	 				$success = true;
	 			}
	 		}
	 		if($success)$res = Entry::find($eid)->entryReference()->orderBy('sort','asc')->get();
    	}elseif($scope==2){
    		$data = ArticleReference::find($id);
	 		$part_id = $data->part_id;
	 		$sort = $data->sort;
	 		if($inputSort){
	 			$referencesSort=ArticleReference::where([['part_id',$part_id],['sort',$inputSort]])->first();
	 			if($referencesSort){
	 				// 如果有sort的更改，先找到要替换sort的对象，如果有，要先替换为当前参考文献的sort
	 				$referencesSort->update([
	 					'sort'=>$sort
	 				]);
		 			
		 		}
				// 然后再将当前参考文献的sort变更为input值
	 			$resultSort = ArticleReference::where('id',$id)->update([
		 					'sort'=>$inputSort
		 				]);

	 			$sortSuc++;
	 			$success = true;
	 		}
	 		if(count($arr)){
	 			if(ArticleReference::where('id',$id)->update($arr)){
	 				$success = true;
	 			}
	 		}
	 		if($success)$res = ArticleReference::where('part_id',$part_id)->orderBy('sort','asc')->get();
    	}
 		return ['success'=>$success,'sortSuc'=>$sortSuc,'references'=>$res];
    }

    //删除参考文献
    public function referenceDelete(Request $request){
    	$id = $request->id;
    	$sort = $request->sort;
    	$pid = $request->pid;
    	$scope = $request->scope;
    	$result = false;
    	$res = [];
    	if($scope==1){
	 		if(EntryReference::where([['entry_id',$pid],['sort','>',$sort]])->exists()){
	 			EntryReference::where([['entry_id',$pid],['sort','>',$sort]])->decrement('sort');
	 		}
	 		$result = EntryReference::referenceDelete($id,$pid,$sort);
	 		if($result)$res = Entry::find($pid)->entryReference()->orderBy('sort','asc')->get();
    	}elseif($scope==2){
	 		if(ArticleReference::where([['part_id',$pid],['sort','>',$sort]])->exists()){
	 			ArticleReference::where([['part_id',$pid],['sort','>',$sort]])->decrement('sort');
	 		}
	 		$result = ArticleReference::articleReferenceDelete($id,$pid,$sort);
	 		if($result)$res = ArticleReference::where('part_id',$pid)->orderBy('sort','asc')->get();	
    	}
    	
    	return ['success' => $result ? true : false,'references'=>$res];
    	
    }
}
