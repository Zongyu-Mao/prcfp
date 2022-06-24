<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;
use App\Events\Classification\ClassificationAddedEvent;
use App\Events\Classification\ClassificationModifiedEvent;
use Laravel\Scout\Searchable;

class Classification extends Model
{
    use Searchable;
    protected $fillable = ['classname','pid','level','creator_id','creator','revisor_id','revisor'];
    public $timestamps = true;

    // 所有分级的分类
    protected function classificationChild(){
        return $this-> hasMany('App\Home\Classification','pid','id');
    }

    public function allClassification() {
        return $this->classificationChild()->with('allClassification');
    }

    //多对多关联专业分类表，取得兴趣专业的用户
    public function getInterestUsers(){
        return $this->belongsToMany('App\Models\User','user_classes','class_id','user_id');
    }

    //得到分类的全路径
    protected function getClassPath($cid) {
        $class4_id = $cid;
        $classname4 = Classification::where('id',$class4_id)->first()->classname;
        $class3_id = Classification::where('id',$class4_id)->first()->pid;
        $classname3 = Classification::where('id',$class3_id)->first()->classname;
        $class2_id = Classification::where('id',$class3_id)->first()->pid;
        $classname2 = Classification::where('id',$class2_id)->first()->classname;
        $class1_id = Classification::where('id',$class2_id)->first()->pid;
        $classname1 = Classification::where('id',$class1_id)->first()->classname;
        return $classPath = array(
                'class1_id'     => $class1_id, 
                'class1_name'   => $classname1, 
                'class2_id'     => $class2_id, 
                'class2_name'   => $classname2, 
                'class3_id'     => $class3_id, 
                'class3_name'   => $classname3, 
                'class4_id'     => $class4_id,
                'class4_name'   => $classname4,
            );
    }

    // 添加分类
    protected function addClass($classname,$pid,$level,$creator_id,$creator){
        $result = Classification::create([
            'classname' => $classname,
            'pid'       => $pid,
            'level'     => $level,
            'creator_id'=> $creator_id,
            'creator'   => $creator
        ]);
        if($result->id){
            event(new ClassificationAddedEvent($result));
        }
        return $result;
    }

    // 修改分类
    protected function modifyClass($classname,$id,$revisor_id,$revisor){
        $result = Classification::where('id',$id)->update([
            'classname' => $classname,
            'revisor_id'=> $revisor_id,
            'revisor'   => $revisor
        ]);
        if($result){
            event(new ClassificationModifiedEvent(Classification::find($id)));
        }
        return $result ? '1':'0';
    }
}
