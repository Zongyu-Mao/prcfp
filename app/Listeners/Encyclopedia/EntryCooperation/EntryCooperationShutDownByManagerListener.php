<?php

namespace App\Listeners\Encyclopedia\EntryCooperation;

use App\Events\Encyclopedia\EntryCooperation\EntryCooperationShutDownByManagerEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Encyclopedia\EntryCooperation\EntryCooperationShutDownByManagerNotification;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\EntryCooperation\EntryCooperationEvent;
use App\Home\Classification;
use App\Home\Announcement;
use App\Home\UserDynamic;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Home\Personnel\MedalSuit;
use App\Home\Personnel\Inform\PunishRecord;

class EntryCooperationShutDownByManagerListener
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
     * @param  contentCooperationShutDownByManagerEvent  $event
     * @return void
     */
    public function handle(EntryCooperationShutDownByManagerEvent $event)
    {
        //协作计划被主动关闭，公告并通知协作成员（结算才会通知兴趣用户），写入用户（管理员）动态、词条动态、协作事件
        $coo = $event->entryCooperation;
        $content = Entry::find($coo->eid);
        $user = auth('api')->user();
        $createtime = Carbon::now();
        // 拿到惩示章 sort=1 type=4
        $medal_id = MedalSuit::where('type',4)->with('getMedals')->first()->getMedals->where('sort',1)->first()->id;
        // 显然这里的inform_id只能设置为0了，看看后期改进不
        $type=4;
        $endtime = Carbon::now()->addMonths($content->level);
        PunishRecord::punishRecordAdd($medal_id,$user->id,0,$type,$endtime,$createtime);
        // 重置有效的讨论、求助、评选和辩论 目前我们不强制失效，而是该由原组成员接手的继续，由新组成员接手的不耽误
        // $opps = EntryOpponent::where([['eid',$content->id],['status',0]])->pluck('id')->toArray();
        // if($opps->count()){
        //     foreach($opps as $o) {
        //         EntryOpponent::rejectAccept($o,0,'autoSystem',3);
        //     }
        // }
        // $advs = EntryAdvise::where([['eid',$content->id],['status',0]])->pluck('id')->toArray();
        // if($advs->count()){
        //     foreach($advs as $a) {
        //         EntryAdvise::adviseAccept($a,0,'autoSystem',3);
        //     }
        // }
        // $res = EntryResort::where([['eid',$content->id],['status',0]])->pluck('id')->toArray();
        // if($res->count()){
        //     foreach($res as $r) {
        //         EntryResort::adviseAccept($a,3);
        //     }
        // }
        // $review = EntryReview::where([['eid',$content->id],['status','0']])->first();
        // if($review)EntryReview::reviewUpdate($review->id,3);
        // $des = EntryDebate::where([['eid',$content->id],['status','0']])->pluck('id')->toArray();
        // if($des->count()){
        //     foreach($des as $d) {
        //         EntryDebate::debateGiveUp($d,5,'remark');
        //     }
        // }
        // 主动关闭，添加协作事件
        EntryCooperationEvent::cooperationEventAdd($coo->id,$user->id,$user->username,'(自管理员)主动关闭并退出了协作计划。');
        // 添加事件到用户动态
        $behavior = '主动退出并关闭了协作计划：';
        $objectName = $coo->title;
        $objectURL = '/encyclopedia/cooperation/'.$content->id.'/'.$content->title;
        $fromName = '百科内容：'.$content->title;
        $fromURL = '/encyclopedia/reading/'.$content->id.'/'.$content->title;
        UserDynamic::dynamicAdd($user->id,$user->username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
        // 发布公告，1代表百科，1代表协作计划
        Announcement::announcementAdd(1,1,'词条《'.$content->title.'》的协作计划<'.$coo->title.'>已经关闭,自管理员已经退出内容自管理。','/encyclopedia/cooperation/'.$content->id.'/'.$content->title,$createtime);
        // 获取协作成员（通知对象）
        $crewArr = $coo->crews()->pluck('user_id')->toArray();
        // 发送通知
        $usersToNotification = User::whereIn('id',$crewArr)->get();
        Notification::send($usersToNotification, new EntryCooperationShutDownByManagerNotification($coo));
    }
}
