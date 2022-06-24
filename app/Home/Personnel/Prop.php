<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Prop\PropModifiedEvent;

class Prop extends Model
{
    protected $fillable = ['sort','name','credit','introduction'];

    public $timestamps = true;

    //写入
    protected function propAdd($sort,$name,$credit,$introduction) {
        $result = Prop::create([
            'sort'  	=> $sort,
            'name'  	=> $name,
            'credit'		=>$credit,
            'introduction'  =>$introduction
        ]);
        event(new PropModifiedEvent($result));
        return $result->id;
    }

    //修改
    protected function propModify($id,$sort,$name,$credit,$introduction) {
        $result = Prop::where('id',$id)->update([
            'sort'      => $sort,
            'name'  	=> $name,
            'credit'	=>$credit,
            'introduction'      =>$introduction
        ]);
        event(new PropModifiedEvent(Prop::find($id)));
        return $result;
    }

    //删除
    protected function propDelete($id) {
        $result = Prop::where('id',$id)->delete();
        return $result ? '1':'0';
    }
}
