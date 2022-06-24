<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['scope','belong','matter','url','createtime'];
    public $timestamps = false;

    protected function announcementAdd($scope,$belong,$matter,$url,$createtime) {
        $result = Announcement::create([
            'scope'   	=> $scope,
            'belong'	=> $belong,
            'matter'	=>$matter,
            'url'     	=>$url,
            'createtime'=>$createtime,
        ]);
        return $result->id;
    }
}
