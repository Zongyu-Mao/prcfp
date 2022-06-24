<?php

namespace App\Home\Publication\Recommend;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\Recommend\ArticleRecommendationUpdatedEvent;

class ArticleRecommendation extends Model
{
    protected $fillable = ['cid','aid'];

    public $timestamps = true;

    //写入
    protected function recommendationAdd($cid,$aid) {
        $result = ArticleRecommendation::create([
            'cid'  	=> $cid,
            'aid'  	=> $aid,
        ]);
        event(new ArticleRecommendationUpdatedEvent($result));
        return $result;
    }

    //修改
    protected function recommendationupdate($id,$aid) {
        $result = ArticleRecommendation::where('id',$id)->update([
            'aid'  	=> $aid,
        ]);
        event(new ArticleRecommendationUpdatedEvent(ArticleRecommendation::find($id)));
        return $result ? '1':'0';
    }
}
