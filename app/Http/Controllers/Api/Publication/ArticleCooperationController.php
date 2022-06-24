<?php

namespace App\Http\Controllers\Api\Publication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Events\Publication\ArticleCooperation\ArticleCooperationAssignModifiedEvent;
use App\Home\Publication\ArticleCooperation\ArticleCooperationDiscussion;
use App\Home\Publication\ArticleCooperation\ArticleCooperationMessage;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation\ArticleCooperationVote;
use App\Home\Publication\ArticleCooperation;
use App\Home\Publication\Article;
use App\Home\Classification;
use App\Home\UserDynamic;
use App\Models\User;

class ArticleCooperationController extends Controller
{
    //展示首页
    public function articleCooperation(Request $request,$id,$articleTitle){
        $article = Article::find($id);
        if($article){
        	$cooperation = ArticleCooperation::where('id',$article->cooperation_id)->with('crews')->with('contributions')->first();
	        if($cooperation) {
	        	$cooperationId = $cooperation->id;
		    	$level = $article->level;
		    	$data_class = Classification::getClassPath($cooperation->cid);
		        
		        //判断讨论表中是否有关于本词条协作计划的讨论，如果有，取出，如果没有，返回空
		        $discussion = ArticleCooperationDiscussion::where('cooperation_id',$cooperationId)->get();
		        //读取协作投票信息
	            $data_votes = ArticleCooperationVote::where([['cooperation_id',$cooperationId],['status','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
	            $history_votes=ArticleCooperationVote::where([['cooperation_id',$cooperationId],['status','>','0']])->orderBy('created_at','desc')->with('getVoteRecord')->get();
	            $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
	            array_push($crewArr,$article->manage_id);
	            // dd($data_votes);
	            $userDynamics = UserDynamic::whereIn('user_id',$crewArr)->orderBy('createtime','desc')->limit('30')->get();
	            // dd($data_votes);

		        //读取协作计划的事件和动态
	            $data_events = ArticleCooperationEvent::where('cooperation_id',$cooperationId)->orderBy('created_at','desc')->limit(15)->get();
		        
		        //读取协作计划面板的用户留言
	            $data_message = ArticleCooperationMessage::where([['cooperation_id',$cooperationId],['pid','0']])->orderBy('created_at','desc')->with('reply')->get();
	            $data_reply = ArticleCooperationMessage::where([['cooperation_id',$cooperationId],['pid','!=','0']])->orderBy('created_at','desc')->get();

		        $crews = User::whereIn('id',$crewArr)->with('getAvatar')->get();
		        $return = array(
		            'basic'       => $article,
		            'cooperation'   => $cooperation,
		            'crews'   		=> $crews,
		            'crewArr'   	=> $crewArr,
		            'data_class'    => $data_class,
		            'discussion' 	=> $discussion,
		            'votes'   		=> $data_votes,
		            'history_votes' => $history_votes,
		            'data_events' 	=> $data_events,
		            'userDynamics' 	=> $userDynamics,
		            'data_message' 	=> $data_message,
		            'data_reply' 	=> $data_reply
		        );
	        }else {
	        	$return = array(
		            'article'       => $article,
		            'cooperation'   => $cooperation,
		        );
	        }
	    	return $return;
	    }
    }

    public function assign(Request $request,$id){
        $data = ArticleCooperation::find($id)->assign;
    	if($request->isMethod('post')){
            //接收改过的任务描述并写入数据表
            $assign = $request->get('assign');
            // return $data1;
    		if($assign != $data){
    			$result = ArticleCooperation::where('id',$id)->update([
					'assign' => $assign,
				]);
				event(new ArticleCooperationAssignModifiedEvent(ArticleCooperation::find($id)));
    		}else{
    			$result = 0;
    		}
    		return $result ? '1':'0';
		}else{  
			return view('home/publication/cooperation/assign',compact('data','id'));
		}
    }
}
