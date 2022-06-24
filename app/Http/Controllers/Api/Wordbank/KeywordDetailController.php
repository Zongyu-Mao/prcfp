<?php

namespace App\Http\Controllers\Api\Wordbank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Keyword;

class KeywordDetailController extends Controller
{
    // 得到keyword和辖之内容
    public function keyword(Request $request) {
    	// 关键词对一个的文章，全部加载，不做分页
    	$data = $request->data;
        $type = $data['type'];
        $keywordname = $data['keyword'];
        $keyword = Keyword::find($data['id']);
        $entries = [];
        $articles = [];
        $exams = [];
        if($keyword->keyword === $keywordname) {
        	if($type===1) {
	        	$entries = $keyword->entries;
	        } else if ($type === 2) {
	        	$articles = $keyword->articles;
	        } else if ($type === 3) {
	        	$exams = $keyword->exams;
	        }
        }
        
        return $content = [
        	'type1' => $type===1,
        	'type2' => $type===2,
        	'type3' => $type===3,
        	'keyword' => $keyword,
        	'type' => $type,
        	'entries' => $entries,
        	'data' => $data,
        	'articles' => $articles,
        	'exams' => $exams
        ];
    }
}
