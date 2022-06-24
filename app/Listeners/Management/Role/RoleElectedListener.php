<?php

namespace App\Listeners\Management\Role;

use App\Events\Management\Role\RoleElectedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Management\Role\RoleElectUpdated;
use App\Notifications\Management\Role\RoleElectSuccess;
use App\Models\Committee\Committee;
use App\Home\Classification;
use App\Models\User;
use Carbon\Carbon;

class RoleElectedListener
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
     * @param  RoleElectedEvent  $event
     * @return void
     */
    public function handle(RoleElectedEvent $event)
    {
        // 申请角色成功后触发的事件
        $elect = $event->roleElectRecord;
        // 靠status状态判断事件
        if($elect->status==0){
            // 创建
        }else if($elect->status>0){
            if($elect->status==1){
                // 要给被推举者发送成功通知
                $user = User::find($elect->elect_id);
                
                // 成功，加入管理组,这里由于role没有level，因此直接按照sort给level了
                $role = $elect->role;
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
                    // L0
                    $title = Classification::find($tcid)->classname;
                    $hierarchy=0;
                }
                // 没有就直接创建，管理者使用id为1的用户，目前假定id为1的用户为创建者
                $committee_id = Committee::where([['cid',$cid],['hierarchy',$hierarchy]])->exists()?Committee::where([['cid',$cid],['hierarchy',$hierarchy]])->first()->id:Committee::newCommittee($title,$tcid,$scid,$thcid,$cid,$hierarchy,'path','introduction',1,1);
                User::committeeUpdate($user->id,$role->id,$committee_id);
                $user->notify(new RoleElectSuccess($elect));
            }
            // 成功得到身份,给推举人发送通知
            $user = User::find($elect->user_id)->notify(new RoleElectUpdated($elect));
        }
    }
}
