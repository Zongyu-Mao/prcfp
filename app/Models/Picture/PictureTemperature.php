<?php

namespace App\Models\Picture;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Picture\PictureTemperatureUpdatedEvent;

class PictureTemperature extends Model
{
    use HasFactory;
    protected $fillable = ['picture_id','temperature'];

    // public $timestamps = true;

    // 关联著作
    public function basic(){
        return $this->belongsTo('App\Models\Picture\Picture','picture_id','id');
    }

    //写入、创建热度，由于创建会有初始热度，因此tem不default0，可能会很少用上这个方法，考虑是否撤掉。
    protected function temperatureAdd($picture_id,$temperature) {
        $result = PictureTemperature::create([
            'picture_id'  	=> $picture_id,
            'temperature'  	=> $temperature
        ]);
        return $result->id;
    }

    //更新热度
    protected function recommendationUpdate($id,$temperature) {
        $result = PictureTemperature::where('id',$id)->update([
            'temperature'  	=> $temperature
        ]);
        // 这里要引发的是热度更新的事件，检查是否需要更新推荐表，目前推荐表考虑实时更新
        event(new PictureTemperatureUpdatedEvent(PictureTemperature::find($id)));
        return $result ? '1':'0';
    }

    // 初始化热度记录
    protected function recordInitialization($picture_id){
    	if(!PictureTemperature::where('picture_id',$picture_id)->exists()){
    		$result = PictureTemperature::create([
	            'picture_id'  	=> $picture_id,
	            'temperature'  	=> 0
	        ]);
	    return $result;
    	}
    }
}
