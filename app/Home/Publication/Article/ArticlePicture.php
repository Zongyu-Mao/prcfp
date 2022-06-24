<?php

namespace App\Home\Publication\Article;

use Illuminate\Database\Eloquent\Model;

class ArticlePicture extends Model
{
    public $timestamps = true;

    protected $fillable = ['aid','title','source','url','author','like','unlike'];

    protected function articlePictureAdd($aid,$title,$source,$url,$author){
    	$result = ArticlePicture::create([
            'aid'   => $aid,
            'title'   => $title,
            'source'   => $source,
            'url'   => $url,
            'author' => $author,
        ]);
        return $result->id;
    }

    protected function avatarUpdate($id,$url){
        $result = ArticlePicture::where('id',$id)->update([
            'url'   => $url
        ]);
        return $result;
    }
}
