<?php

namespace App\Http\Controllers\Api\Encyclopedia\Entry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Models\Encyclopedia\Ambiguity\Synonym;
use App\Models\Encyclopedia\Ambiguity\Polysemant;
use Illuminate\Support\Facades\Auth;
use App\Home\Classification;
use Carbon\Carbon;

class AmbiguityController extends Controller
{
    // 同义或多义的处理，这里只有一个内容移动是跨百科著作试卷的，其他都是百科内部内容
    public function ambiguity(Request $request) {
    	$data = $request->data;
    	$type = $data['type'];
        $eid = $data['eid'];
        $user = Auth::user();
        $createtime = Carbon::now();
        $result = 0;
        $backContent = '';
        // 1是合并词条 2是歧义关联 3是移动class 4是新建同义项,合并或拆分词条只能针对status为1或2
        if($type==1) {
            $id = $data['id'];
            $p_entry = Entry::find($eid);
            $entry = Entry::find($id);
            // 被合并对象status应该是没有附带的0或1，合并主词条
            if($p_entry->status<2 ){
                if($entry->status<5) {
                    $title = $data['title'];
                    $isMergeCrews = $data['isMergeCrews'];
                    $isEmptyCrews = $data['isEmptyCrews'];
                    $crewArr1=[];
                    // 合并词条即清空cooperation，改变status，
                    if($isMergeCrews==1) {
                        // 合并crews,为保留记录，不会清空原来的crew，考虑给前段选择是否销毁协作计划
                        $cooperation = EntryCooperation::find($p_entry->cooperation_id);
                        if($cooperation)$crewArr1 = $cooperation->crews()->pluck('user_id')->toArray();
                        $crewArr = $crewArr1;
                        array_push($crewArr,$p_entry->manage_id);
                        if(in_array($entry->manage_id,$crewArr))array_diff($crewArr, [$entry->manage_id]);
                        foreach($crewArr as $crew) {
                            if(!EntryCooperationUser::where([['cooperation_id',$entry->cooperation_id],['user_id',$crew]])->exists()){
                                EntryCooperationUser::cooperationMemberJoin($entry->cooperation_id,$crew,$createtime);
                            }
                        }
                    }
                    if($isEmptyCrews==2) {
                        $coo_id = $p_entry->cooperation_id;
                        foreach($crewArr1 as $crew) {
                            EntryCooperationUser::where([['cooperation_id',$coo_id],['user_id',$crew]])->delete();
                        }
                    }
                    // 清空id词条的信息,保存部分必要信息
                    Entry::where('id',$id)->update([
                        'surveillance'=>0,
                        'cooperation_id'=>0,
                        'status'=>5, //同义词副词
                        'manage_id'=>$entry->manage_id, 
                        'lasteditor_id'=>$user->id, 
                    ]);
                }
                
                // 组成同义词
                $result = Synonym::newSynonym($eid,$id,$user->id,$createtime);
                
                // if($entry->status<2)Entry::where('id',$eid)->update(['status'=>2]);
                // if($entry->status==3)Entry::where('id',$eid)->update(['status'=>4]);
                // 由于合并了内容，因此entry本身更改了状态，且新增了同义词
                if($result){
                    $basic = Entry::where('id',$eid)->with('entryAvatar')->first();
                    $sids = Synonym::where('eid',$eid)->pluck('sid')->toArray();
                    $backContent = Entry::whereIn('id',$sids)->get(); //$synonyms
                }
            }
        }else if($type==2) {
            // 歧义关联只能关联正常的、同名的词条，但是不排除同义主词条，即status为1或2
            $id = $data['id'];
            $entry = Entry::find($eid);
            $p_entry = Entry::find($id);
            if($entry->title == $p_entry->title &&
                $p_entry->status<4 &&
                $entry->status<4 &&
                !Polysemant::where([['eid',$eid],['poly_id',$id]])->exists() &&
                !Polysemant::where([['eid',$id],['poly_id',$eid]])->exists()) {
                $result = Polysemant::newPolysemant($eid,$id,$user->id,$createtime);
            }
            
            if($result) {
                // if($entry->status<2)Entry::where('id',$id)->update(['status'=>3]);
                // if($entry->status==2)Entry::where('id',$id)->update(['status'=>4]);
                // if($p_entry->status<2) Entry::where('id',$eid)->update(['status'=>3]);
                // if($p_entry->status==2) Entry::where('id',$eid)->update(['status'=>4]);
                $poly_ids1 = Polysemant::where('eid',$eid)->pluck('poly_id')->toArray();
                $poly_ids2 = Polysemant::where('poly_id',$eid)->pluck('eid')->toArray();
                $p_ids = array_unique(array_merge($poly_ids1,$poly_ids2));
                $basic = Entry::where('id',$eid)->with('entryAvatar')->first();
                $backContent = Entry::whereIn('id',$p_ids)->get(); //$polysemants
            }
        }else if($type==3) {
            // 移动，这个是分scope的
            $classD_id = $data['classD_id'];
            $classA_id = $data['classA_id'];
            $scope = $data['scope'];

            if($scope==1)$content = Entry::find($data['content_id']);
            if($scope==2)$content = Article::find($data['content_id']);
            if($scope==3)$content = Exam::find($data['content_id']);
            if($scope==4)$content = Group::find($data['content_id']);
            if($content->cid != $classD_id) {
                if($scope==1){
                    $result = Entry::where('id',$data['content_id'])->update(['cid'=>$classD_id]);
                    if($result){
                        $basic=Entry::where('id',$data['content_id'])->with('entryAvatar')->first();
                        $backContent = Classification::getClassPath($basic->cid); //$data_class
                    }
                } else if ($scope==2){
                    $result = Article::where('id',$data['content_id'])->update(['cid'=>$classD_id]);
                    if($result){
                        $basic=Article::where('id',$data['content_id'])->with('articleAvatar')->first();
                        $backContent = Classification::getClassPath($basic->cid);
                    }
                } else if ($scope==3){
                    $result = Exam::where('id',$data['content_id'])->update(['cid'=>$classD_id]);
                    if($result){
                        $basic=Exam::where('id',$data['content_id'])->first();//这里试卷暂时不加avatar
                        $backContent = Classification::getClassPath($basic->cid);
                    }
                } else if ($scope==4){
                    $result = Group::where('id',$data['content_id'])->update(['cid'=>$classD_id]);
                    if($result){
                        $basic=Group::where('id',$data['content_id'])->with('groupEmblem')->first();
                        $backContent = Classification::getClassPath($basic->cid);
                    }
                }

            }
        }else if($type==4) {
            // 同义词
            $title = $data['title'];
            $etitle = $data['etitle'];
            $cid = $data['classD_id'];
            $classA_id = $data['classA_id'];
            $entry = Entry::find($eid);
            $status = 5;
            $result = Entry::entrySynonymCreate($cid,$title,$etitle,$user->id,$status,$user->id,$user->id);
            $result = Synonym::newSynonym($eid,$result,$user->id,$createtime);
            // if($entry->status<2)Entry::where('id',$eid)->update(['status'=>2]);
            // if($entry->status==3)Entry::where('id',$eid)->update(['status'=>4]);
            if($result){
                $basic = Entry::where('id',$eid)->with('entryAvatar')->first();
                $sids = Synonym::where('eid',$basic->id)->pluck('sid')->toArray();
                $backContent = Entry::whereIn('id',$sids)->get(); //$synonyms
            }
        }
    	return ['success'=>$result?true:false,'basic'=>$basic,'backContent'=>$backContent];
    }

    public function content_check(Request $request) {
    	// 这里只是check内容是否存在
    	$data = $request->data;
    	$type = $data['type'];
    	$eid = $data['eid'];
    	$content = '';
        $contentArr = [];
        $hasStatus = [];
        $polysemantArr = [];
    	$polysemant = false;
    	if($type==1){
    		// type为1是确认id的词条，这个词条是要返回比较的，合并的词条不能在歧义和同义表内
    		$id = $data['id'];
            $s = Synonym::where('eid',$id)->exists();
            $p = Polysemant::where('eid',$id)->orWhere('poly_id',$id)->exists();
    		$content = Entry::find($id);
            $hasStatus = ['s'=>$s,'p'=>$p];
    	}elseif($type==2){
            // 2，拆分歧义，那么应该确定是否已经在歧义表里了
            $id = $data['id'];
            $content = Entry::find($id);
            $polysemantArr = Polysemant::where([['eid',$eid],['poly_id',$id]])->orWhere([['eid',$id],['poly_id',$eid]])->exists();
        }elseif($type==3){
    		// 3是添加同义词，返回内容只是为了比较
    		$title = $data['title'];
    		$contentArr = Entry::where('title',$title)->pluck('id')->toArray();
    	}elseif($type==5){
            // 5是主内容导航来的确认
            $id = $data['id'];
            $content = Entry::find($id);
            $entry='';
            if($content->status==5){
                $entry = Entry::find(Synonym::where('sid',$id)->first()->eid);
            }
            $content = [
                'content'=>$content,
                'entry'=>$entry,
            ];
        }
    	return $res = [
    		'content'=>$content,
            'contentArr'=>$contentArr,
    		'polysemantArr'=>$polysemantArr,
    	];
    }
}
