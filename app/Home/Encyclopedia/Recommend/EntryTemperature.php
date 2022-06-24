<?php

namespace App\Home\Encyclopedia\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Encyclopedia\Recommend\EntryTemperatureUpdatedEvent;

class EntryTemperature extends Model
{
    protected $fillable = ['eid','temperature'];

    public $timestamps = true;

    // 关联词条
    public function getEntry(){
        return $this->belongsTo('App\Home\Encyclopedia\Entry','eid','id');
    }

    //写入、创建热度，由于创建会有初始热度，因此tem不default0
    protected function temperatureAdd($eid,$temperature) {
        $result = EntryTemperature::create([
            'eid'  	=> $eid,
            'temperature'  	=> $temperature
        ]);
        return $result->id;
    }

    //更新热度
    protected function recommendationUpdate($id,$temperature) {
        $result = EntryTemperature::where('id',$id)->update([
            'temperature'  	=> $temperature
        ]);
        // 这里要引发的是热度更新的事件，检查是否需要更新推荐表，目前推荐表考虑实时更新
        // 由于排行榜更新在redis中实现，因此此处不再触发事件，甚至可以考虑推荐表的存废了****************************
        // event(new EntryTemperatureUpdatedEvent(EntryTemperature::find($id)));
        return $result ? '1':'0';
    }

    // 初始化热度记录
    protected function recordInitialization($eid){
    	if(!EntryTemperature::where('eid',$eid)->exists()){
    		$result = EntryTemperature::create([
	            'eid'  	=> $eid,
	            'temperature'  	=> 0
	        ]);
	    return $result;
    	}
    }
}
