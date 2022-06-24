<?php

namespace App\Listeners\Personal\Credit;

use App\Events\Personal\Credit\UserCreditChangedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Personal\Credit\LevelUpgradedNotification;
use App\Notifications\Personal\Credit\LevelDemotedNotification;
use App\Home\Personnel\Level\UserLevel;
use App\Home\Personnel\Level;
use App\Models\User;

class UserCreditChangedListener
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
     * @param  UserCreditChangedEvent  $event
     * @return void
     */
    public function handle(UserCreditChangedEvent $event)
    {
        //积分的变化有经验值和成长值的变化,成长值是固定不变的 用来参考积分的增减
        // 本事件不记录经验值变化的原因，记录是在具体事件监听器中记录，本事件的功能是复核积分增减是否引起等级角色的变化
        $old = $event->user;
        $new = User::find($old->id);
        // 得到用户当前等级id
        $user_level = $old->getLevel;
        if(!$user_level){
            // 复核并初始化level
           $user_level =  UserLevel::levelInitialization($old->id,$old->grow_value);
        }
        $level = Level::find($user_level->id);
        // 成长值**************
        // 判断成长值的增减,如果成长值增加了，判断是否需要升级，如果需要升级，判断是否需要通知
        if($new->grow_value > $old->grow_value && $old->grow_value > 0){

            if($new->grow_value > $level->creditshigher){
                // 新的成长值大于老的等级分数上限，需要升级
                // 找到合适的等级
                $newLevel = Level::where([['creditslower','<=',$new->grow_value],['creditshigher','>=',$new->grow_value]])->first();
                UserLevel::where('id',$user_level->id)->update(['level_id'=>$newLevel->id]);
                // 如果存在倒扣状态
                $backs = $user_level->status-1;
                $levelUps = $newLevel->sort - $level->sort;
                if($levelUps >= $backs){
                    // 如果不存在倒扣状态
                    // 通知用户升级了,注意传输的模型不是用户等级，用户等级是里面的sort
                    $new->notify(new LevelUpgradedNotification($new->getLevel));
                    UserLevel::where('id',$user_level->id)->update(['status'=>1]);
                }else{
                    UserLevel::where('id',$user_level->id)->update(['status'=>$backs-$levelUps+1]);
                }
            }
            
        } 
        // 只有等级在4级以上的用户，才能使用倒扣权利，但是这里只是复核，不需要考虑等级，倒扣有专门的控制器和事件
        // 成长值的减少、降级
        if($new->grow_value < $level->creditslower && $new->grow_value >= 0){
            // 新的成长值小yu老的等级分数下限，需要降级，降级需要更新等级和倒扣状态
            // 找到合适的等级
            $newLevel = Level::where([['creditslower','<=',$new->grow_value],['creditshigher','>=',$new->grow_value]])->first();
            // 降级会有倒扣状态的更新,倒扣值为新旧等级差
            $backs = $level->sort - $newLevel->sort;

            UserLevel::where('id',$user_level->id)->update([
                'level_id'=>$newLevel->id,
                'status'=>$user_level->status+$backs
            ]);
            // 通知用户降级了
            $new->notify(new LevelDemotedNotification($new->getLevel));
        }

        // 积分（经验值）的变化
        
    }
}
