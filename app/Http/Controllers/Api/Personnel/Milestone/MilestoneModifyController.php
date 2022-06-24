<?php

namespace App\Http\Controllers\Api\Personnel\Milestone;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Milestone;

class MilestoneModifyController extends Controller
{
    //更改属性
    public function milestoneModify(Request $request){
    	$data = $request->data;
        $id = $data['id'];
        $milestone = Milestone::find($id);
        $isCreate = $data['isCreate'];
        $name = $data['name'];
        $sort = $data['sort'];
        $type = $data['type'];
        $introduction = $data['introduction'];
        $ms = '';
        if($isCreate) {
            if(Milestone::where('sort',$sort)->exists()){
                $changes = Milestone::where('sort','>=',$sort)->get();
                foreach($changes as $value){
                    Milestone::where('id',$value->id)->update([
                        'sort'=>$value->sort+1
                    ]);
                }
            }
            $result = Milestone::milestoneAdd($sort,$name,$type,$introduction);
        } else {
            if($sort != $milestone->sort){
                // 需要更改sort，先得到更改对象，如果有，直接与之交换sort
                Milestone::where('sort',$sort)->update(['sort'=>$milestone->sort]);
            }
            $result = Milestone::milestoneModify($id,$sort,$name,$type,$introduction);
        }
        if($result)$ms = Milestone::orderBy('sort','asc')->get();
		return [
    		'success'	=> $result? true:false,
            'milestones'    =>  $ms
    	];
    }

    //删除id的里程碑
    public function milestoneDelete(Request $request){
    	$id = $request->input('id');
    	$result = Milestone::milestoneDelete($id);
        if($result)$ms = Milestone::orderBy('sort','asc')->get();
    	return [
    		'success'	=> $result? true:false,
            'milestones'    =>  $ms
    	];
    }
}
