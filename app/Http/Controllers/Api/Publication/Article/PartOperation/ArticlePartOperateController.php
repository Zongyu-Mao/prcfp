<?php

namespace App\Http\Controllers\Api\Publication\Article\PartOperation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Publication\Article\ArticlePart;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article\Reference\ArticleReference;

class ArticlePartOperateController extends Controller
{
    //在id之前添加新的章节
    public function articlePartAdd(Request $request){
    	$data = $request->data;
    	$isForward = $data['isForward'];
    	$lock = 0;
		$title = $data['title'];
		$sort = $data['sort'];
		$user_id = Auth::user()->id;
		$aid = $data['aid'];
		$result = false;
        if($sort===0){
            $sort = 1;
            $result = ArticlePart::newPart($aid,$title,$sort,$user_id);
        } else {
            // 提交内容
            if($isForward){
                if(ArticlePart::where('aid',$aid)->where('sort','>=',$sort)->exists()) {
                    ArticlePart::where('aid',$aid)->where('sort','>=',$sort)->increment('sort');
                }
                // 这里记录一个小错误，本来change用的是sort，但是sort判定条件where([['aid',$aid],['sort',$change]])会把所有的sort都加1，
                // 因为$change+1后，仍然符合下一循环的筛选条件，所以改为使用id，并将此id的sort+1
                $result = ArticlePart::newPart($aid,$title,$sort,$user_id);
            }elseif(!$isForward){
                $newSort = $sort+1;
                // 这里做个筛选，如果sort==0，即代表没有内容时的创建，此时不再确认其他sort的情况
                if($sort!=0){
                    if(ArticlePart::where('aid',$aid)->where('sort','>',$sort)->exists()) {
                        ArticlePart::where('aid',$aid)->where('sort','>',$sort)->increment('sort');
                    }
                }
                
                $result = ArticlePart::newPart($aid,$title,$newSort,$user_id);
            }
        }
    	
        // 注意article是分章节有参考文献的，所以这里一定要带参考文献重新返回
        if($result)$parts=ArticlePart::where('aid',$aid)->orderBy('sort','asc')->get();
    	return ['success'=>$result ? true:false,'parts'=>$parts,'part'=>$result];
    }


    // 删除章节
    public function articlePartDelete(Request $request){
        // part 删除还要删除contents references
    	$data = $request->data;
        $id = $data['id'];
        $part = ArticlePart::find($id);
    	$sort = $part->sort;
    	$aid = $part->aid;
    	$result = false;
        if(ArticlePart::where('aid',$aid)->where('sort','>',$sort)->exists()) {
            ArticlePart::where('aid',$aid)->where('sort','>',$sort)->decrement('sort');
        }
  //   	$changeArr = ArticlePart::where('aid',$aid)->where('sort','>',$sort)->pluck('id')->toArray();
		// foreach($changeArr as $change){
		// 	ArticlePart::where('id',$change)->update(['sort'=>ArticlePart::find($change)->sort-1]);
		// }
        // event(new ArticlePartDeletedEvent(ArticlePart::find($id)));
    	$result = ArticlePart::where('id',$id)->delete();
    	// 注意，删除章节，要将章节有关的参考文献一起删除
        ArticleReference::where('part_id',$id)->delete();
    	ArticleContent::where('part_id',$id)->delete();
    	if($result) {
            $parts=ArticlePart::where('aid',$aid)->orderBy('sort','asc')->get();
            $part = $parts->where('sort',$sort>1?$sort-1:1)->first();
            $contents = $part->contents;
            $references = $part->references;
        }
        return ['success'=>$result ? true:false,'parts'=>$parts,'part'=>$part,'contents'=>$contents,'references'=>$references];
    }
}
