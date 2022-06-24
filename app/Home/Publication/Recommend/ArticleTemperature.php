<?php

namespace App\Home\Publication\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Recommend\ArticleTemperatureUpdatedEvent;

class ArticleTemperature extends Model
{
    protected $fillable = ['aid','temperature'];

    public $timestamps = true;

    // 关联著作
    public function getArticle(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    //写入、创建热度，由于创建会有初始热度，因此tem不default0，可能会很少用上这个方法，考虑是否撤掉。
    protected function temperatureAdd($aid,$temperature) {
        $result = ArticleTemperature::create([
            'aid'  	=> $aid,
            'temperature'  	=> $temperature
        ]);
        return $result->id;
    }

    //更新热度
    protected function recommendationUpdate($id,$temperature) {
        $result = ArticleTemperature::where('id',$id)->update([
            'temperature'  	=> $temperature
        ]);
        // 这里要引发的是热度更新的事件，检查是否需要更新推荐表，目前推荐表考虑实时更新
        event(new ArticleTemperatureUpdatedEvent(ArticleTemperature::find($id)));
        return $result ? '1':'0';
    }

    // 初始化热度记录
    protected function recordInitialization($aid){
    	if(!ArticleTemperature::where('aid',$aid)->exists()){
    		$result = ArticleTemperature::create([
	            'aid'  	=> $aid,
	            'temperature'  	=> 0
	        ]);
	    return $result;
    	}
    }
}
