<?php

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamQuestion;
use App\Models\Picture\Picture;
use App\Models\Committee\Committee;
use App\Home\Organization\Group;
use App\Home\Classification;
use App\Models\Document\Document;
use App\Home\Keyword;

class SearchController extends Controller
{
    //
    public function search(Request $request) {
        // 主内容结果分两类，主内容表搜索和主内容contents表搜索,exam包含question搜索，但是不会包含options
        $data = $request->data;
        $keyword = $data['keyword'];
        $scope = $data['scope']??'homearea';
        $pageSize = $data['pageSize'];
        $es = $as = $exs = $gs = $cs = $ms = $ds = $ps = $ws = '';
        if($scope=='homearea') {
            $es = Entry::search($keyword)->paginate($pageSize);
            $as = Article::search($keyword)->paginate($pageSize);
            $exs = Exam::search($keyword)->paginate($pageSize);
        } else if ($scope=='encyclopedia') {
            $es = Entry::search($keyword)->paginate($pageSize);
        } else if($scope=='publication') {
            $as = Article::search($keyword)->paginate($pageSize);
        } else if($scope=='examination') {
            $exs = Exam::search($keyword)->paginate($pageSize);
        } else if($scope=='organization') {
            $gs = Group::search($keyword)->paginate($pageSize);
        } else if($scope=='classification') {
            $cs = Classification::search($keyword)->paginate($pageSize);
        } else if($scope=='committee') {
            $ms = Committee::search($keyword)->paginate($pageSize);
        } else if($scope=='document') {
            $ds = Document::search($keyword)->paginate($pageSize);
        } else if($scope=='picture') {
            $ps = Picture::search($keyword)->paginate($pageSize);
        } else if($scope=='wordbank') {
            $ws = Keyword::search($keyword)->paginate($pageSize);
        }
        
        // $data = Book::where('name', 'LIKE','%'.$request->keyword.'%')->get();
        // return response()->json($data); 
    	// $result = EntryContent::search($keyword,"'attributesToHighlight':['overview']")->get();
    	return $result=[
            'entries'   =>$es,
            'articles'   =>$as,
            'exams'   =>$exs,
            'groups'   =>$gs,
            'classes'   =>$cs,
            'committees'   =>$ms,
            'documents'   =>$ds,
            'pictures'   =>$ps,
            'words'   =>$ws,
        ];
    }

    public function searchContent(Request $request) {
        // contents必须要有scope，不能为全局
        $data = $request->data;
        $keyword = $data['keyword'];
        $scope = $data['scope'];
        if($scope=='homearea')$scope='encyclopedia';
        $pageSize = $data['pageSize'];
        $result = '';
        if($scope=='encyclopedia') {
            $result = EntryContent::search($keyword)
                ->query( function($query) {
                    $query->with('basic');
                })->paginate($pageSize);
        } else if($scope=='publication') {
            $result = ArticleContent::search($keyword)
                ->query( function($query) {
                    $query->with('basic');
                })->paginate($pageSize);
        } else if($scope=='examination') {
            $result = ExamQuestion::search($keyword)
                ->query( function($query) {
                    $query->with('basic');
                })->paginate($pageSize);
        }
        return $result;
    }
}
