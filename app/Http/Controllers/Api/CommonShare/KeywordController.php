<?php

namespace App\Http\Controllers\Api\CommonShare;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Encyclopedia\Entry\EntryKeywordModifiedEvent;
use App\Events\Publication\Article\ArticleKeywordModifiedEvent;
use App\Home\Keyword;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use DB;

class KeywordController extends Controller
{
    //词条文章关键字的修改
    public function keywordsModify(Request $request){
    	$keywords = $request->keywords;
    	// 该id是主内容id
    	$id = $request->id;
    	$scope = $request->scope;
    	$count = 0;
    	$res=[];
    	for($i=0;$i<count($keywords);$i++){
    		$keyword = $keywords[$i];
    		if($keyword['keyword']){
    			// 如果有keyword，代表修改了关键词且没有置空，此时找到新的keyword的id，并对应入关系，并删除旧的关系
    			// return $res = [$keyword['id'] => $keyword['keyword']];
    			// 这里不考虑关键词是否是前端新增的，处理都是一样的，只是旧的关系有没有或删不删的处理而已
    			// 1、查看keyword表中是否存在该keyword
    			$isHasKeyword = Keyword::where('keyword',$keyword['keyword'])->exists()?'1':'0';
    			if($isHasKeyword == '0'){
                	//如果关键词不存在，向关键词表中添加该关键词
                	$keywordCreate = Keyword::create([
                		'keyword' => $keyword['keyword']
                	]);
                	//得到插入的关键词id并写入关系表中
                	$keyword_id = $keywordCreate->id;
                	if($scope==1){
                		$relationResult = DB::table('entry_keyword')->insert([
	                		'entry_id'	 => $id,
	                		'keyword_id' => $keyword_id
	                	]);
                	}elseif($scope==2){
                		$relationResult = DB::table('article_keywords')->insert([
	                		'article_id'	 => $id,
	                		'keyword_id' => $keyword_id
	                	]);
                	}elseif($scope==3){
                        $relationResult = DB::table('exam_keywords')->insert([
                            'exam_id'     => $id,
                            'keyword_id' => $keyword_id
                        ]);
                    }
                	

                	$count++;
                }elseif($isHasKeyword == '1'){
                	//如果关键词已经存在，仅需要添加关系表或更新旧的关系表
                	$keyword_id = Keyword::where('keyword',$keyword['keyword'])->first()->id;
                	if($scope==1){
                		if(!DB::table('entry_keyword')->where([['entry_id',$id],['keyword_id',$keyword_id]])->exists()){
		                    DB::table('entry_keyword')->insert([
		                		'entry_id'	 => $id,
		                		'keyword_id' => $keyword_id
		                	]);
                		}
                	}elseif($scope==2){
                		if(!DB::table('article_keywords')->where([['article_id',$id],['keyword_id',$keyword_id]])->exists()){
                			DB::table('article_keywords')->insert([
		                		'article_id'	 => $id,
		                		'keyword_id' => $keyword_id
		                	]);
                		}
                	}elseif($scope==3){
                        if(!DB::table('exam_keywords')->where([['exam_id',$id],['keyword_id',$keyword_id]])->exists()){
                            DB::table('exam_keywords')->insert([
                                'exam_id'     => $id,
                                'keyword_id' => $keyword_id
                            ]);
                        }
                    }
                	
                	$count++;
            	}
    		}
    		// 无论关键词是否为空，只要存在有效的id，就一定是修改了，因此旧的id关系肯定要删除
    		if($keyword['id']){
    			if($scope==1){
	    			DB::table('entry_keyword')->where([['entry_id',$id],['keyword_id',$keyword['id']]])->delete();
	    			$count++;
		    		// 删除没有引用的关键词
		    		if(!DB::table('entry_keyword')->where('keyword_id',$keyword['id'])->count()){
		    			Keyword::find($keyword['id'])->delete();
		    		}
	    		}elseif($scope==2){
	    			DB::table('article_keywords')->where([['article_id',$id],['keyword_id',$keyword['id']]])->delete();
	    			$count++;
		    		if(!DB::table('article_keywords')->where('keyword_id',$keyword['id'])->count()){
		    			Keyword::find($keyword['id'])->delete();
		    		}
	    		}elseif($scope==3){
                    DB::table('exam_keywords')->where([['exam_id',$id],['keyword_id',$keyword['id']]])->delete();
                    $count++;
                    if(!DB::table('exam_keywords')->where('keyword_id',$keyword['id'])->count()){
                        Keyword::find($keyword['id'])->delete();
                    }
                }
    		}
    		
    		
    	}
        if($count){
            if($scope===1){
                $ks = Entry::find($id)->keywords()->get();
            }else if ($scope===2) {
                $ks = Article::find($id)->keywords()->get();
            }else if ($scope===3) {
                $ks = Exam::find($id)->keywords()->get();
            }
        }
    	return ['success'=>$count ? true:false,'keywords'=>$ks];
    	// return count($keywords);
    	// return $res;
     }
}
