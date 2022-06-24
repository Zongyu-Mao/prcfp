<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Keyword extends Model
{
    use Searchable;
    protected $fillable = ['keyword'];

    //多对多关联
    public function entries(){
        return $this->belongsToMany('App\Home\Encyclopedia\Entry','entry_keyword','keyword_id','entry_id');
    }

    public function articles(){
        return $this->belongsToMany('App\Home\Publication\Article','article_keywords','keyword_id','article_id');
    }

    public function exams(){
        return $this->belongsToMany('App\Home\Examination\Exam','exam_keywords','keyword_id','exam_id');
    }

    
}
