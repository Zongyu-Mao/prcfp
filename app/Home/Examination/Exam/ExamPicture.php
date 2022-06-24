<?php

namespace App\Home\Examination\Exam;

use Illuminate\Database\Eloquent\Model;

class ExamPicture extends Model
{
    public $timestamps = true;

    protected $fillable = ['exam_id','title','source','url','author','likes','unlikes'];

    protected function examPictureAdd($exam_id,$title,$source,$url,$author){
    	$result = ExamPicture::create([
            'exam_id'   => $exam_id,
            'title'   => $title,
            'source'   => $source,
            'url'   => $url,
            'author' => $author,
        ]);
        return $result->id;
    }

    protected function avatarUpdate($id,$url){
        $result = ExamPicture::where('id',$id)->update([
            'url'   => $url
        ]);
        return $result;
    }
}
