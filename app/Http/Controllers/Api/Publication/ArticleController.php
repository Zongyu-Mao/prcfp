<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Publication\Article\ArticleContent;
use App\Home\Publication\Article\Reference\ArticleReference;
use App\Home\Publication\Article;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Recommend\ArticleTemperatureRecord;
use App\Home\Publication\Recommend\ArticleTemperature;
use App\Home\Personnel\Behavior;
use App\Home\Classification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use App\Models\Committee\Surveillance\SurveillanceArticleRecord;
use App\Models\Committee\Surveillance\SurveillanceArticleMark;
use App\Models\Committee\Surveillance\SurveillanceArticleWarning;
use App\Models\Publication\Article\ArticlePart;

class ArticleController extends Controller
{
    //著作详情页面
    public function articleDetail(Request $request,$id,$articleTitle){
    	$data = Article::find($id);
        $return = [];
     	if($id && $articleTitle==$data->title){
            $parts=ArticlePart::where('aid',$id)->orderBy('sort','asc')->get();
            $part = $parts->where('sort',1)->first();
            $contents = $part->contents;
     		$references = $part->references;
            // $data->getArticleContents()->get();
            // 这里目前不考虑用Article模型的一对多关联，直接在articleContent模型里取content内容
            // dd($articleContents);
            Redis::INCR('article:views:'.$data->id);
            Redis::INCR('article:temperature:'.$data->id);
            // 更新排行榜热度
            Redis::ZINCRBY('article:temperature:rank',1,$data->id);
            // 分类榜
            Redis::ZINCRBY('article:classification:temperature:rank:'.$data->cid,1,$data->id);
            Redis::ZINCRBY('classification:temperature:rank',1,$data->cid);
            // 此处热度是在Redis下，没有在Cache下
     		$temperature = Redis::GET('article:temperature:'.$data->id);
     		// dd($entryContent);
            $surveillances = SurveillanceArticleRecord::where('sid',$id)->exists()?SurveillanceArticleRecord::where('sid',$id)->get():'';
            $marks = SurveillanceArticleMark::where('sid',$id)->exists()?SurveillanceArticleMark::where('sid',$id)->get():'';
            $warnings = SurveillanceArticleWarning::where('sid',$id)->exists()?SurveillanceArticleWarning::where('sid',$id)->get():'';

 			$cid = $data->cid;
            $user = auth('api')->user();
            $role = $user->getRole;
            $committee = $user->getCommittee;
 			$user_id = $user->id;
            $cooperation = ArticleCooperation::find($data->cooperation_id);
            $crewArr = $cooperation?$cooperation->crews()->pluck('user_id')->toArray():[];
            $data->manage_id?array_push($crewArr,$data->manage_id):'';

     		$data_class = Classification::getClassPath($cid);
     		$ex_entries = $data->extendedEntryReadings()->get();
            $ex_articles = $data->extendedArticleReadings()->get();
            $ex_exams = $data->extendedExamReadings()->get();
     		$focus = $data->articleFocus()->find($user_id);
     		$collect = $data->articleCollect()->find($user_id);
     		$keywords = $data->keywords()->get();
     		// dd($keywords);
            $behavior_id = 1;
            $rec_check = ArticleTemperatureRecord::where([['aid',$id],['behavior_id',$behavior_id],['user_id',$user_id]])->count();
     		$articleExtraData = [
     			'focus' => $focus,
     			'collect'=>$collect
     		];
	     	$return = [
                'article' => $data,
                'class' => $data_class,
                'crewArr' => $crewArr,
                'part_id' => $part->id,
                'part' => $part,
                'parts' => $parts,
                'contents' => $contents,
                'references' => $references,
                'ex_entries' => $ex_entries,
                'ex_articles' => $ex_articles,
                'ex_exams' => $ex_exams,
                'focus' => $focus,
                'collect' => $collect,
                'keywords' => $keywords,
                'rec_check' => $rec_check,
                'surveillances' => $surveillances,
                'marks' => $marks,
                'warnings' => $warnings,
                'tem' => $temperature,
                'user' => $user,
            ];
     	}
     	return $return;
    }

    // 获取partContents
    public function getPartContents(Request $request) {
        $data = $request->data;
        $part_id = $data['part_id'];
        $part = ArticlePart::where('id',$part_id)->with('contents')->with('references')->first();
        return $part;
    }
}
