<?php

namespace App\Http\Controllers\Api\Encyclopedia\Entry\ChapterOperations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Events\Encyclopedia\Entry\EntryContentDeletedEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChapterOperateController extends Controller
{
    //在id之前添加新的章节
    public function entryChapterAdd(Request $request){
        $content_id = $request->id;//这个id现在时木有用的
    	$input = $request->content;
    	$isForward = $input['isForward'];
		$content = $input['content'];
		$sort = $input['sort'];
		$user_id = auth('api')->user()->id;
		$ip = User::getClientIp();
		$big = $input['big'];
		$reason = $input['reason'];
		$eid = $input['entry_id'];
		$result = false;
        if($sort==0){
            $result = EntryContent::entryContentCreate($eid,1,$content,$user_id,$ip,$big,$reason);
        } else{
            // 提交内容
            if($isForward){
                $changeArr = EntryContent::where('eid',$eid)->where('sort','>=',$sort)->pluck('id')->toArray();
                // 这里记录一个小错误，本来change用的是sort，但是sort判定条件where([['eid',$eid],['sort',$change]])会把所有的sort都加1，
                // 因为$change+1后，仍然符合下一循环的筛选条件，所以改为使用id，并将此id的sort+1
                foreach($changeArr as $change){
                    EntryContent::where('id',$change)->update(['sort'=>EntryContent::find($change)->sort+1]);
                }
                $result = EntryContent::entryContentCreate($eid,$sort,$content,$user_id,$ip,$big,$reason);
            }elseif(!$isForward){
                $newSort = $sort+1;
                if($sort!=0){
                    // 在id之后添加，只需要大于id的全部增加，而目前创建的sort也要在$sort的基础上加1，因为他是在sort后面添加章节
                    $changeArr = EntryContent::where('eid',$eid)->where('sort','>',$sort)->pluck('id')->toArray();
                    // 好像空的数组$changeArr也不会报错，暂时不判断$changeArr是不是为空了
                    foreach($changeArr as $change){
                        EntryContent::where('id',$change)->update(['sort'=>EntryContent::find($change)->sort+1]);
                    }
                }
                
                $result = EntryContent::entryContentCreate($eid,$newSort,$content,$user_id,$ip,$big,$reason);
            }
        }
    	if($result)$contents=EntryContent::where('eid',$eid)->orderBy('sort','asc')->get();
    	return ['success'=>$result ? true:false,'contents'=>$contents];
    }


    // 删除章节
    public function entryChapterDelete(Request $request){
    	$id = $request->content_id;
    	$sort = $request->sort;
    	$eid = $request->entry_id;
    	$result = false;
    	$changeArr = EntryContent::where('eid',$eid)->where('sort','>',$sort)->pluck('id')->toArray();
		foreach($changeArr as $change){
			EntryContent::where('id',$change)->update(['sort'=>EntryContent::find($change)->sort-1]);
		}
        event(new EntryContentDeletedEvent(EntryContent::find($id)));
    	$result = EntryContent::where('id',$id)->delete();
        if($result)$contents=EntryContent::where('eid',$eid)->orderBy('sort','asc')->get();
    	return ['success'=>$result ? true:false,'contents'=>$contents];
    }
}
