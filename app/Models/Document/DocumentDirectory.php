<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Document\DocumentDirectoryModifiedEvent;

class DocumentDirectory extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'classname', 'pid', 'level','creator_id','revisor_id'
    ];
    // 所有分级的分类
    protected function directoryChild(){
        return $this-> hasMany('App\Models\Document\DocumentDirectory','pid','id');
    }

    public function allDirectories() {
        return $this->directoryChild()->with('allDirectories');
    }

    //得到分类的全路径
    protected function getPath($cid) {
        $class2_id = $cid;
        $class2 = DocumentDirectory::find($cid);
        $classname2 = $class2->classname;
        $class1_id = $class2->pid;
        $classname1 = DocumentDirectory::find($class1_id)->classname;
        return $classPath = array(
                'class1_id'     => $class1_id, 
                'class1_name'   => $classname1, 
                'class2_id'     => $class2_id, 
                'class2_name'   => $classname2, 
            );
    }

    // 添加分类
    protected function addDirectory($classname,$pid,$level,$creator_id){
        $result = DocumentDirectory::create([
            'classname' => $classname,
            'pid'       => $pid,
            'level'     => $level,
            'creator_id'=> $creator_id
        ]);
        event(new DocumentDirectoryModifiedEvent($result));
        return $result->id;
    }

    // 修改分类
    protected function modifyDirectory($classname,$id,$revisor_id){
        $result = DocumentDirectory::where('id',$id)->update([
            'classname' => $classname,
            'revisor_id'=> $revisor_id
        ]);
        event(new DocumentDirectoryModifiedEvent(DocumentDirectory::find($id)));
        return $result;
    }

}
