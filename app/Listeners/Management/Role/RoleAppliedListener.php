<?php

namespace App\Listeners\Management\Role;

use App\Events\Management\Role\RoleAppliedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Role\RoleApplyUpdated;
use App\Models\User;
use App\Models\Committee\Committee;
use App\Home\Classification;
use Carbon\Carbon;

class RoleAppliedListener
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
     * @param  RoleAppliedEvent  $event
     * @return void
     */
    public function handle(RoleAppliedEvent $event)
    {
        // 申请角色成功后触发的事件
        $apply = $event->roleApplyRecord;
        $createtime = Carbon::now();
        // 靠status状态判断事件，status为3的失效也记录
        if($apply->status==0){
            // 创建
        }else if($apply->status>0){
            // 结果已出,发送通知
            $user = User::find($apply->user_id);
            
            if($apply->status==1){
                // 成功，加入管理组,这里由于role没有level，因此直接按照sort给level了
                $role = $apply->role;
                $sort = $role->sort; 
                // 由于角色只能涉及主专业，因此四个class_id先准备好
                $cid = $user->specialty;
                $thcid = Classification::find($cid)->pid;
                $scid = Classification::find($thcid)->pid;
                $tcid = Classification::find($scid)->pid;
                if($sort>=2&&$sort<=4){
                    // L4组
                    $title = Classification::find($cid)->classname;
                    $hierarchy=4;
                    
                } else if($sort == 5) {
                    // L3
                    $title = Classification::find($thcid)->classname;
                    $hierarchy=3;
                } else if($sort == 6) {
                    // L2
                    $title = Classification::find($scid)->classname;
                    $hierarchy=2;
                } else if($sort >= 7 && $sort <=10) {
                    // L1
                    $title = Classification::find($tcid)->classname;
                    $hierarchy=1;
                } else if($role->sort >= 11) {
                    // L0其实不用 只有一个
                    $cid = 0;
                    $thcid=0;
                    $scid=0;
                    $tcid=0;
                    $title = Classification::find($tcid)->classname;
                    $hierarchy=0;
                }
                // 没有就直接创建，管理者使用id为1的用户，目前假定id为1的用户为创建者
                $committee_id = Committee::where([['cid',$cid],['hierarchy',$hierarchy]])->exists()?Committee::where([['cid',$cid],['hierarchy',$hierarchy]])->first()->id:Committee::newCommittee($title,$tcid,$scid,$thcid,$cid,$hierarchy,'path','introduction',1,1);
                User::committeeUpdate($user->id,$role->id,$committee_id);
                $user->notify(new RoleApplyUpdated($apply));
            }
        }
    }
}
