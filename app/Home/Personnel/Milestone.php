<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Milestone\MilestoneModifiedEvent;

class Milestone extends Model
{
    protected $fillable = ['sort','name','type','introduction'];

    public $timestamps = true;

    //写入
    protected function milestoneAdd($sort,$name,$type,$introduction) {
        $result = Milestone::create([
            'sort'  	=> $sort,
            'name'		=> $name,
            'type'		=> $type,
            'introduction'	=> $introduction,
        ]);
        event(new MilestoneModifiedEvent($result));
        return $result->id;
    }

    //修改
    protected function milestoneModify($id,$sort,$name,$type,$introduction) {
        $result = Milestone::where('id',$id)->update([
            'sort'      => $sort,
            'name'		=> $name,
            'type'		=> $type,
            'introduction'	=> $introduction,
        ]);
        event(new MilestoneModifiedEvent(Milestone::find($id)));
        return $result;
    }

    //删除
    protected function milestoneDelete($id) {
        $result = Milestone::where('id',$id)->delete();
        return $result ? '1':'0';
    }
}
