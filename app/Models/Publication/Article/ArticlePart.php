<?php

namespace App\Models\Publication\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class ArticlePart extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['aid','title','sort', 'creator_id'];

    public function basic(){
        return $this -> hasOne('App\Home\Publication\Article','id','aid');
    }

    public function getCreator(){
        return $this -> belongsTo('App\Models\User','creator_id','id');
    }

    // 一对多关联参考文献
    public function references(){
        return $this -> hasMany('App\Home\Publication\Article\Reference\ArticleReference','part_id','id')->orderBy('sort','asc');
    }
    // 一对多关联内容
    public function contents(){
        return $this -> hasMany('App\Home\Publication\Article\ArticleContent','part_id','id')->orderBy('sort','asc');
    }


    // 新建part
    protected function newPart($aid,$title,$sort,$creator_id){
        $result = ArticlePart::create([
            'aid'   => $aid,
            'title'   => $title,
            'sort'   => $sort,
            'creator_id' => $creator_id
        ]);
        return $result;
    }

    // 修改part
    protected function partModify($id,$title){
        $result = ArticlePart::where('id',$id)->update([
            'title'   => $title
        ]);
        return $result;
    }

}
