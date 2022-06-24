<?php

namespace App\Home;

use Illuminate\Database\Eloquent\Model;

class SpecialPage extends Model
{
    protected $fillable = ['title','summary','content','manager_id','lasteditor_id'];
    //新建特殊页面
    protected function pageCreate($title,$summary,$content,$manager_id,$lasteditor){
    	$pageArray = array(
    		'title'		=> $title,
    		'summary'		=> $summary,
    		'content'	=> $content,
    		'manager_id'	=> $manager_id,
    		'lasteditor_id'	=> $lasteditor,
    	   );
    	$page = new SpecialPage;
    	$result = $page -> fill($pageArray) -> save();
        return $page -> id;
    }
}
