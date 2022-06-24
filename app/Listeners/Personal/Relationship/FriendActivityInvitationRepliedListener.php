<?php

namespace App\Listeners\Personal\Relationship;

use App\Events\Personal\Relationship\FriendActivityInvitationRepliedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Relationship\FriendActivityInvitationRepliedNotification;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationUser;
use App\Home\Encyclopedia\EntryCooperation;
use App\Home\Publication\ArticleCooperation\ArticleCooperationEvent;
use App\Home\Publication\ArticleCooperation\ArticleCooperationUser;
use App\Home\Publication\ArticleCooperation;
use App\Home\Examination\ExamCooperation;
use App\Home\Examination\ExamCooperation\ExamCooperationEvent;
use App\Home\Examination\ExamCooperation\ExamCooperationUser;
use App\Home\Encyclopedia\Entry;
use App\Home\Publication\Article;
use App\Home\Examination\Exam;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupUser;
use Carbon\Carbon;
use App\Models\User;

class FriendActivityInvitationRepliedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FriendActivityInvitationRepliedEvent  $event
     * @return void
     */
    public function handle(FriendActivityInvitationRepliedEvent $event)
    {
        $record = $event->friendActivityInvitationRecord;
        // 如果对方同意加入，要做很多工作
        if($record->inviteResult == '1'){
            if($record->type == 1){
                // 加入词条协作小组
                $cooperation = EntryCooperation::find($record->type_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                $entry = Entry::find($cooperation->eid);
                array_push($crewArr,$entry->manage_id);
                array_unique($crewArr);
                $createtime = Carbon::now();
                // 如果用户不在协作小组内
                if(!in_array($record->invite_id, $crewArr)){
                    EntryCooperationUser::cooperationMemberJoin($record->type_id,$record->invite_id,$createtime);
                    // 写入协作事件
                    EntryCooperationEvent::cooperationEventAdd($record->type_id,$record->invite_id,$record->invite_username,'由['.$record->username.']邀请进入协作小组。');
                }
                
            }elseif($record->type == 2){
                // 加入著作协作小组
                $cooperation = ArticleCooperation::find($record->type_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                $article = Article::find($cooperation->aid);
                array_push($crewArr,$article->manage_id);
                array_unique($crewArr);
                $createtime = Carbon::now();
                // 如果用户不在协作小组内
                if(!in_array($record->invite_id, $crewArr)){
                    ArticleCooperationUser::cooperationMemberJoin($record->type_id,$record->invite_id,$createtime);
                    // 写入协作事件
                    ArticleCooperationEvent::cooperationEventAdd($record->type_id,$record->invite_id,$record->invite_username,'由['.$record->username.']邀请进入协作小组。');
                }
            }elseif($record->type == 3){
                // 加入试卷协作小组
                $cooperation = ExamCooperation::find($record->type_id);
                $crewArr = $cooperation->crews()->pluck('user_id')->toArray();
                $exam = Exam::find($cooperation->exam_id);
                array_push($crewArr,$cooperation->manage_id);
                array_unique($crewArr);
                $createtime = Carbon::now();
                // 如果用户不在协作小组内
                if(!in_array($record->invite_id, $crewArr)){
                    ExamCooperationUser::cooperationMemberJoin($record->type_id,$record->invite_id,$createtime);
                    // 写入协作事件
                    ExamCooperationEvent::cooperationEventAdd($record->type_id,$record->invite_id,$record->invite_username,'由['.$record->username.']邀请进入协作小组。');
                }
            }elseif($record->type == 4){
                // 加入组织小组
                $group = Group::find($record->type_id);
                $crewArr = $group->members()->pluck('user_id')->toArray();
                array_push($crewArr,$group->manage_id);
                array_unique($crewArr);
                $createtime = Carbon::now();
                // 如果用户不在组织内
                if(!in_array($record->invite_id, $crewArr)){
                    $position = 1;
                    GroupUser::groupMemberJoin($record->invite_id,$record->type_id,$position,$createtime);
                    // 写入事件
                    GroupEvent::groupEventAdd($record->type_id,$record->invite_id,$record->invite_username,'由['.$record->username.']邀请进入组织。');
                }
            }
        }

        //回复邀请后，告知邀请者态度
        User::find($record->user_id)->notify(new FriendActivityInvitationRepliedNotification($record));
        
    }
}
