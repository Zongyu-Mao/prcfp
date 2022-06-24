<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;

class EntryPicture extends Model
{
    //关联表格
    public $timestamps = true;

    protected $fillable = ['eid','title','source','url','author','like','unlike'];

    protected function entryPictureAdd($eid,$title,$source,$url,$author,$like,$unlike){
    	$result = EntryPicture::create([
            'eid'   => $eid,
            'title'   => $title,
            'source'   => $source,
            'url'   => $url,
            'author' => $author,
            'like'		=> $like,
            'unlike'=> $unlike,
        ]);
        return $result->id;
    }

    protected function avatarUpdate($id,$url){
        $result = EntryPicture::where('id',$id)->update([
            'url'   => $url
        ]);
        return $result;
    }
}
