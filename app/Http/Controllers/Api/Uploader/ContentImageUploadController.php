<?php

namespace App\Http\Controllers\Api\Uploader;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publication\Article\ArticlePart;

class ContentImageUploadController extends Controller
{
    //
    public function contentImageUpload(Request $request,$scope,$obj,$id) {
    	$filename = '';
    	$path = '';
        if ($request -> hasFile('file') && $request -> file('file') -> isvalid()) { //判断文件上传是否有效

        	$filename = sha1(time() . $request -> file('file') -> getClientOriginalName()) . '.' . $request -> file('file') -> getClientOriginalExtension();
    		//文件保存
    		if($scope==1){
    			$m='entries/entry'.$id;
    		}elseif($scope==2){
                $id = ArticlePart::find($id)->aid;
    			$m='articles/article'.$id;
    		}elseif($scope==3){
    			$m='exams/exam'.$id;
    		}elseif($scope==4){
                $m='groups/group'.$id;
            }elseif($scope==5){
                $m='classes/class'.$id;
            }elseif($scope==6){
                $m='committees/committee'.$id;
            }elseif($scope==7){
                $m='documents/document'.$id;
            }elseif($scope==8){
                $m='pictures';
            }elseif($scope==9){
                $m='wordbank';
            }elseif($scope==10){
                $m='votes';
            }elseif($scope==101){
    			$m='suits/medalsuit'.$id;
    		}elseif($scope==102){
    			$m='privateMedal'.$id;
    		}elseif($scope==66){
                $m='informs';
            }
    		if($scope&&$scope!=66){
				if($obj==1){
					// 1是主内容的图片
					$path = $request -> file('file')->storeAs(
	                    $m.'/content',$filename, 'public'
	                );
				}elseif($obj==2){
					// 2是协作内容的图片（目前不做区分了）
					$path = $request -> file('file')->storeAs(
	                    $m.'/cooperation',$filename, 'public'
	                );
				}elseif($obj==3){
					// 3是评选内容的图片（目前不做区分了）
					$path = $request -> file('file')->storeAs(
	                    $m.'/review',$filename, 'public'
	                );
				}elseif($obj==4){
					// 4是求助内容的图片（目前不做区分了）
					$path = $request -> file('file')->storeAs(
	                    $m.'/resort',$filename, 'public'
	                );
				}elseif($obj==5){
					// 5是讨论内容的图片（目前不做区分了）
					$path = $request -> file('file')->storeAs(
	                    $m.'/discussion',$filename, 'public'
	                );
				}elseif($obj==6){
					// 6是攻辩内容的图片（目前不做区分了）
					$path = $request -> file('file')->storeAs(
	                    $m.'/debate',$filename, 'public'
	                );
				}
                
            } elseif($scope==66){
            	$basic_inform = ['entry','article','exam','groupDoc'];
            	$judge_inform=['entryReviewDiscussion','entryReviewDiscussion','entryReviewAdvise','entryReviewOpponent','entryResort','entryResortHelp','entryDiscussion','entryAdvise','entryOpponent','entryDebateA','entryDebateB','entryDebateR','articleReviewDiscussion','articleReviewDiscussion','articleReviewAdvise','articleReviewOpponent','articleResort','articleResortHelp','articleDiscussion','articleAdvise','articleOpponent','articleDebateA','articleDebateB','articleDebateR','examReviewDiscussion','examReviewDiscussion','examReviewAdvise','examReviewOpponent','examResort','examResortHelp','examDiscussion','examAdvise','examOpponent','examDebateA','examDebateB','examDebateR',];
            	$message_inform=['entryCooperationMessage','entryReviewDiscussion','entryResortSupportComment','entryDebateComment','articleCooperationMessage','articleReviewDiscussion','articleResortSupportComment','articleDebateComment','examCooperationMessage','examReviewDiscussion','examResortSupportComment','examDebateComment','groupDocComment'];
            	// 举报不同于其他内容，多了一层目录
            	// 举报的参数分别是inform-launch/scope/obj/id?target_id,obj对应type，id对应inform_scope，target_id对应对象id
            	switch($obj){
            		case(1):
            		$m.='/basic/';
            		$m.=$basic_inform[$id];
            		case(2):
                    $m.='/judgement/';
                    $m.=$judge_inform[$id];
                    case(3):
                    $m.='/message/';
                    $m.=$message_inform[$id];
            	}
                $m.=$request->target_id;
                $path = $request -> file('file')->storeAs(
                    $m,$filename, 'public'
                );
            }
    		// $path = '/storage/' . $filename;
    		
        }
        return ['location'=> $path];
    }
}
