<?php

namespace App\Home\Publication;

use Illuminate\Database\Eloquent\Model;
use App\Events\Publication\ArticleDebate\ArticleDebateCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateBOpeningCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateAFDCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateBFDCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateAClosingStatementCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateBClosingStatementCreatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateAutomaticallyClearedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateClear\ArticleDebateTimeOutByRefereeClearedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateTimeOutClosedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateClosed\ArticleDebateGivenUpEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateRefereeJoinedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateAnalyseUpdatedEvent;
use App\Events\Publication\ArticleDebate\ArticleDebateReferee\ArticleDebateSummarySubmittedEvent;

class ArticleDebate extends Model
{
    protected $fillable = ['aid','cid', 'type', 'type_id', 'title', 'referee_id', 'referee', 'deadline', 'Aauthor_id', 'Aauthor', 'Bauthor_id', 'Bauthor','ARedstars','ABlackstars','BRedstars','BBlackstars','RRedstars','RBlackstars','AopeningStatement','BopeningStatement','BOScreateTime','AfreeDebate','AFDcreateTime','BfreeDebate','BFDcreateTime','AclosingStatement', 'ACScreateTime','BclosingStatement', 'BCScreateTime','summary','summaryTime','analyse','analyseTime','status','victory','views','heat','remark'];

    // 一对多关联点赞记录表，获得点赞记录
    public function getStars(){
        return $this->hasMany('App\Home\Publication\ArticleDebate\ArticleDebateStarRecord','debate_id','id');
    }

    // 一对一关联,获得著作信息
    public function getContent(){
        return $this->belongsTo('App\Home\Publication\Article','aid','id');
    }

    // 一对一关联组长信息
    public function managerInfo(){
        return $this->belongsTo('App\Models\User','Aauthor_id','id');
    }

    //创建（转）辩论第一轮，攻方开启辩论
    protected function debate_create($aid,$cid,$type,$type_id,$title,$deadline,$Aauthor_id,$Aauthor,$Bauthor_id,$Bauthor,$AOS,$remark){
    	$debateCreateArray = array(
            'aid'       => $aid,
    		'cid'		=> $cid,
    		'type'		=> $type,
    		'type_id'	=> $type_id,
    		'title'		=> $title,
    		'deadline'	=> $deadline,
    		'Aauthor_id'	=> $Aauthor_id,
    		'Aauthor'		=> $Aauthor,
    		'Bauthor_id'	=> $Bauthor_id,
    		'Bauthor'		=> $Bauthor,
            'AopeningStatement'     => $AOS,
    		'remark'		=> $remark,
    	   );
    	$debate = new ArticleDebate;
    	$result = $debate -> fill($debateCreateArray) -> save();
        if($debate->id){
            event(new ArticleDebateCreatedEvent($debate));
        }
    	return $debate->only('id','type','type_id');
    }

    //辩方接受并开始辩方开篇
    protected function debate_bopening($id,$BOS,$BOStime,$remark){
    	$AOS = ArticleDebate::find($id)->AopeningStatement;
    	if($id && $AOS && $BOS)
    		$result = ArticleDebate::where('id',$id)->update([
    			'BopeningStatement'	=> $BOS,
                'BOScreateTime' => $BOStime,
                'remark' => $remark,
    		]);
        if($result){
            // 触发辩方开篇陈词事件
            event(new ArticleDebateBOpeningCreatedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }

    //辩方发表开篇后，攻方进入自由辩论
    protected function debate_AFreeDebate($id,$AFD,$AFDtime,$remark){
    	$BOS = ArticleDebate::find($id)->BopeningStatement;
    	if($id && $BOS && $AFD)
    		$result =  ArticleDebate::where('id',$id)->update([
    			'AfreeDebate'	=> $AFD,
                'AFDcreateTime' => $AFDtime,
                'remark' => $remark,
    		]);
        if($result){
            // 触发攻方自由辩论事件
            event(new ArticleDebateAFDCreatedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }

    //攻方发表自由辩论后，辩方进入自由辩论
    protected function debate_BFreeDebate($id,$BFD,$BFDtime,$remark){
    	$AFD = ArticleDebate::find($id)->AfreeDebate;
    	if($id && $AFD && $BFD)
    		$result =  ArticleDebate::where('id',$id)->update([
    			'BfreeDebate'	=> $BFD,
                'BFDcreateTime' => $BFDtime,
                'remark' => $remark,
    		]);
        if($result){
            // 触发辩方自由辩论事件
            event(new ArticleDebateBFDCreatedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }

    //辩方发表自由辩论后，攻方进入总结陈词
    protected function debate_AClosingStatement($id,$ACS,$ACStime,$remark){
    	$BFD = ArticleDebate::find($id)->BfreeDebate;
    	if($id && $BFD && $ACS)
    		$result =  ArticleDebate::where('id',$id)->update([
    			'AclosingStatement'	=> $ACS,
                'ACScreateTime' => $ACStime,
                'remark' => $remark,
    		]);
        if($result){
            // 触发辩方自由辩论事件
            event(new ArticleDebateAClosingStatementCreatedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }

    //攻方发表总结陈词后，辩方进入总结陈词
    protected function debate_BClosingStatement($id,$BCS,$BCStime,$remark){
    	$ACS = ArticleDebate::find($id)->AclosingStatement;
    	if($id && $ACS && $BCS)
    		$result =  ArticleDebate::where('id',$id)->update([
    			'BclosingStatement'	=> $BCS,
                'BCScreateTime' => $BCStime,
                'remark' => $remark,
    		]);
        if($result){
            // 触发辩方自由辩论事件
            event(new ArticleDebateBClosingStatementCreatedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }

    //成为裁判
    protected function asReferee($id,$referee,$referee_id){
        $result =  ArticleDebate::where('id',$id)->update([
            'referee'   => $referee,
            'referee_id' => $referee_id
        ]);
        if($result){
            event(new ArticleDebateRefereeJoinedEvent(ArticleDebate::find($id)));
        }
        return $result ? '1':'0';
    }

    //裁判分析
    protected function debateAnalyse($id,$analyse,$analyseTime){
        $result =  ArticleDebate::where('id',$id)->update([
            'analyse'   => $analyse,
            'analyseTime' => $analyseTime,
        ]);
        if($result){
            event(new ArticleDebateAnalyseUpdatedEvent(ArticleDebate::find($id)));
        }
        return $result ? '1':'0';
    }

    //裁判总结并行结算
    protected function debate_summary($id,$summary,$status,$remark,$victory){
		$result =  ArticleDebate::where('id',$id)->update([
            'summary'   => $summary,
			'status'	=> $status,
            'remark'    => $remark,
            'victory'   => $victory,
		]);
        if($result){
            event(new ArticleDebateSummarySubmittedEvent(ArticleDebate::find($id)));
        }
    	return $result ? '1':'0';
    }
    protected function debateTimeOutByRefereeClear($id,$status,$remark,$victory){
        $result = ArticleDebate::where('id',$id)->update([
            'status'    => $status,
            'remark'    => $remark,
            'victory'   => $victory
        ]);
        if($result){
            event(new ArticleDebateTimeOutByRefereeClearedEvent(ArticleDebate::find($id)));
        }
        return $result ? '1':'0';
    }

    // 没有裁判的结算
    protected function debate_automatically_clear($id,$status,$remark,$victory){
        $result = ArticleDebate::where('id',$id)->update([
            'status'    => $status,
            'remark'    => $remark,
            'victory'   => $victory
        ]);
        if($result){
            event(new ArticleDebateAutomaticallyClearedEvent(ArticleDebate::find($id)));
        }
        return $result ? '1':'0';
    }

    //辩方发表总结陈词后，由裁判确定结束辩论(本函数运行的前提是辩论正常结束)
    protected function debateTimeOutClose($id,$status,$remark){
		$result =  ArticleDebate::where('id',$id)->update([
			'status'	=> $status,
			'remark'	=> $remark,
		]);
        if($result){
            event(new ArticleDebateTimeOutClosedEvent(ArticleDebate::find($id)));
        }  
    	return $result ? '1':'0';
    }

    //辩论的放弃
    protected function debateGiveUp($id,$status,$remark){
        $result =  ArticleDebate::where('id',$id)->update([
            'status'    => $status,
            'remark'    => $remark,
        ]);
        if($result){
            event(new ArticleDebateGivenUpEvent(ArticleDebate::find($id)));
        }  
        return $result ? '1':'0';
    }
}
