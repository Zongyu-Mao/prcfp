<?php

namespace App\Http\Controllers\Api\Personnel\Prop;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\Prop;

class PropModifyController extends Controller
{
    //更改道具属性
    public function propModify(Request $request){
        $data = $request->data;
    	$id = $data['id'];
        $prop = Prop::find($id);
        $isCreate = $data['isCreate'];
        $name = $data['name'];
        $sort = $data['sort'];
        $credit = $data['credit'];
        $introduction = $data['introduction'];
        $ps='';
        if($isCreate) {
            if(Prop::where('sort',$sort)->exists()){
                $changes = Prop::where('sort','>=',$sort)->get();
                foreach($changes as $value){
                    Prop::where('id',$value->id)->update([
                        'sort'=>$value->sort+1
                    ]);
                }
            }
            $result = Prop::propAdd($sort,$name,$credit,$introduction);
        } else {
            if($sort != $prop->sort){
                // 需要更改sort，先得到更改对象，如果有，直接与之交换sort
                Prop::where('sort',$sort)->update(['sort'=>$prop->sort]);
            }
            $result = Prop::propModify($id,$sort,$name,$credit,$introduction);
        }
		if($result)$ps = Prop::orderBy('sort','asc')->get();
    	return [
    		'success'	=> $result? true:false,
            'props' =>  $ps
    	];
    }

    //删除id的道具，一般肯定不能删除
    public function propDelete(Request $request){
    	$id = $request->input('id');
    	$result = Prop::propDelete($id);
        if($result)$ps = Prop::orderBy('sort','asc')->get();
    	return [
    		'success'	=> $result? true:false,
            'props' =>  $ps
    	];
    }
}
