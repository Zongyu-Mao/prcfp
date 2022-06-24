<?php

namespace App\Http\Controllers\Api\Publication\Article\ChapterOperations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article\Reference\ArticleReference;
use App\Events\Publication\Article\ArticleContentDeletedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ArticleChapterOperateController extends Controller
{
    //在id之前添加新的章节
    public function articleChapterAdd(Request $request){
    	$input = $request->content;
    	$isForward = $input['isForward'];
    	$lock = 0;
		$content = $input['content'];
		$sort = $input['sort'];
		$user_id = auth('api')->user()->id;
		$ip = User::getClientIp();
		$big = $input['big'];
		$reason = $input['reason'];
        $aid = $input['article_id'];
		$part_id = $input['part_id'];
		$result = false;
        if($sort===0){
            $sort = 1;
            $result = ArticleContent::articleContentCreate($aid,$part_id,$sort,$lock,$content,$user_id,$ip,$big,$reason);
        } else {
            // 提交内容
            if($isForward){
                $changeArr = ArticleContent::where('part_id',$part_id)->where('sort','>=',$sort)->pluck('id')->toArray();
                // 这里记录一个小错误，本来change用的是sort，但是sort判定条件where([['aid',$aid],['sort',$change]])会把所有的sort都加1，
                // 因为$change+1后，仍然符合下一循环的筛选条件，所以改为使用id，并将此id的sort+1
                foreach($changeArr as $change){
                    ArticleContent::where('id',$change)->update(['sort'=>ArticleContent::find($change)->sort+1]);
                }
                $result = ArticleContent::articleContentCreate($aid,$part_id,$sort,$lock,$content,$user_id,$ip,$big,$reason);
            }elseif(!$isForward){

                $newSort = $sort+1;
                // 这里做个筛选，如果sort==0，即代表没有内容时的创建，此时不再确认其他sort的情况
                if($sort!=0){
                    // 在id之后添加，只需要大于id的全部增加，而目前创建的sort也要在$sort的基础上加1，因为他是在sort后面添加章节
                    $changeArr = ArticleContent::where('part_id',$part_id)->where('sort','>',$sort)->pluck('id')->toArray();
                    // 好像空的数组$changeArr也不会报错，暂时不判断$changeArr是不是为空了
                    foreach($changeArr as $change){
                        ArticleContent::where('id',$change)->update(['sort'=>ArticleContent::find($change)->sort+1]);
                    } 
                }
                
                $result = ArticleContent::articleContentCreate($aid,$part_id,$newSort,$lock,$content,$user_id,$ip,$big,$reason);
            }
        }
    	
        // 注意article是分章节有参考文献的，所以这里一定要带参考文献重新返回
        if($result)$contents=ArticleContent::where('part_id',$part_id)->orderBy('sort','asc')->get();
    	return ['success'=>$result ? true:false,'contents'=>$contents];
    }


    // 删除章节
    public function articleChapterDelete(Request $request){
    	$id = $request->content_id;
    	$sort = $request->sort;
    	$part_id = $request->part_id;
    	$result = false;
    	$changeArr = ArticleContent::where('part_id',$part_id)->where('sort','>',$sort)->pluck('id')->toArray();
		foreach($changeArr as $change){
			ArticleContent::where('id',$change)->update(['sort'=>ArticleContent::find($change)->sort-1]);
		}
        event(new ArticleContentDeletedEvent(ArticleContent::find($id)));
    	$result = ArticleContent::where('id',$id)->delete();
    	// 注意，删除章节，要将章节有关的参考文献一起删除
    	ArticleReference::where('content_id',$id)->delete();
    	if($result)$contents=ArticleContent::where('part_id',$part_id)->orderBy('sort','asc')->get();
        return ['success'=>$result ? true:false,'contents'=>$contents];
    }
}
