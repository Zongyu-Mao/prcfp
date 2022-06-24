<?php

namespace App\Listeners\Management\Role;

use App\Events\Management\Role\RoleElectReactEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Personnel\Role\RoleElectRecord;
use App\Models\Personnel\Role\RoleElectReactRecord;
use Carbon\Carbon;

class RoleElectReactListener
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
     * @param  RoleElectReactEvent  $event
     * @return void
     */
    public function handle(RoleElectReactEvent $event)
    {
        // 处理
        $record = $event->roleElectReactRecord;
        $elect = $record->elect;
        $role = $elect->role;
        // 这里要看下，需要处理结果，结果就是角色的sort×2且大于反对人数2倍的同意，或者sort×2且大于同意人数的2倍的反对或者超过1个月，失败
        // $countsNeed = $role->sort * 2;
        $countsNeed = 1;
        // 如果过期，直接失败
        $status = 0;
        if(Carbon::now()->addMonths(-1) > $elect->createtime){
            $status = 3;
        }else{
            $A = RoleElectReactRecord::where([['elect_id',$record->elect_id],['stand',1]])->count();
            $B = RoleElectReactRecord::where([['elect_id',$record->elect_id],['stand',2]])->count();
            if(($A > 2*$B) && ($A>= $countsNeed)){
                $status = 1;
            } else if (($B > 2*$A) && ($B>= $countsNeed)) {
                $status = 2;
            }
        }
        if($status>0)RoleElectRecord::electUpdate($record->elect_id,$status);
    }
}
