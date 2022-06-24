<?php

namespace App\Home\Encyclopedia\Entry;

use Illuminate\Database\Eloquent\Model;

class EntryVersion extends Model
{
    //词条的历史版本
    public $timestamps = true;

    protected $fillable = ['eid','author','ip','content','big','reason'];

    // 关联词条内容表
    public function getEntryContent(){
    	return $this -> hasOne('App\Home\Encyclopedia\EntryContent','id','content');
    }

    // 多对多关联用户表，一篇文章多个版本，多个版本多个作者
    public function getUsers(){
    	return $this -> belongsToMany('App\User','entryVersion_user','author_id','user_id');
    }

    protected function entryVersionAdd($eid,$author,$ip,$content,$big,$reason){
    	$result = EntryVersion::create([
            'eid'   	=> $eid,
            'author'	=> $author,
            'ip'		=> $ip,
            'content'   => $content,
            'big' 		=> $big,
            'reason'	=> $reason,
        ]);
        return $result->id;
    }
}
