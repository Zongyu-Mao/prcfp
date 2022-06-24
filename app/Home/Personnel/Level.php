<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Level\LevelModifiedEvent;

class Level extends Model
{
    protected $fillable = ['sort','name','creditslower','creditshigher','introduction'];

    public $timestamps = true;

    //写入
    protected function levelAdd($sort,$name,$creditslower,$creditshigher,$introduction) {
        $result = Level::create([
            'sort'  	=> $sort,
            'name'  	=> $name,
            'creditslower'		=>$creditslower,
            'creditshigher'     =>$creditshigher,
            'introduction'      =>$introduction
        ]);
        event(new LevelModifiedEvent($result));
        return $result->id;
    }

    //修改
    protected function levelModify($id,$name,$creditslower,$creditshigher,$introduction) {
        $result = Level::where('id',$id)->update([
            'name'  	=> $name,
            'creditslower'		=>$creditslower,
            'creditshigher'     =>$creditshigher,
            'introduction'      =>$introduction
        ]);
        event(new LevelModifiedEvent(Level::find($id)));
        return $result;
    }

    //删除
    protected function levelDelete($id) {
        $result = Level::where('id',$id)->delete();
        return $result ? '1':'0';
    }
}
