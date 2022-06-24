<?php

namespace App\Home\Personnel;

use Illuminate\Database\Eloquent\Model;
use App\Events\Personnel\Medal\MedalCreatedEvent;

class Medal extends Model
{
    protected $fillable = ['suit_id','sort','weight','url','title','description','creator_id'];
    // public $timestamps = false;

    // 关联user获得作者
    public function getCreator(){
        return $this->belongsTo('App\Models\User','creator_id','id');
    }

    // 功章的更改应该设置事件
    // event

    // 一对一关联功章表得到所有套件
    public function getSuit(){
        return $this->belongsTo('App\Home\Personnel\MedalSuit','suit_id','id');
    }

	//功章的写入
    protected function medalAdd($suit_id,$sort,$weight,$url,$title,$description,$creator_id) {
        $result = Medal::create([
            'suit_id'	=> $suit_id,
            'sort'  	=> $sort,
            'weight'	=> $weight,
            'url'		=>$url,
            'title'     =>$title,
            'description'	=>$description,
            'creator_id'	=>$creator_id
        ]);
        // event确认功章的数量，改变suit的完成状态！！！！！！！！！！！！！！！！！！！
        event(new MedalCreatedEvent($result));
        return $result->id;
    }

    //功章的属性修改
    protected function medalModify($id,$sort,$weight,$url,$title,$description) {
        $result = Medal::where('id',$id)->update([
            'sort'      => $sort,
            'weight'    => $weight,
            'url'       =>$url,
            'title'   	=> $title,
            'description'	=> $description,
        ]);
        return $result ? '1':'0';
    }

    //功章的删除
    protected function medalDelete($id) {
    	$medal =Medal::find($id);
    	$modify = Medal::where([['suit_id',$medal->suit_id],['sort','>',$medal->sort]])->orderBy('sort','asc')->get();
			if(count($modify)>0){
				foreach($modify as $value){
				Medal::where('id',$value->id)->update([
					'sort' => $value->sort-1
					]);
				}
			}
		// event 还要删除图片图片图片，删除会面临惩罚惩罚惩罚
        // 更改状态
        $suit = $medal->getSuit;
        $result = Medal::where('id',$id)->delete();
        if(count($suit->getMedals) < $suit->amount){
            $status = 1;
            MedalSuit::statusUpdate($suit->id,$status);
        } 
        return $result ? '1':'0';
    }
}
