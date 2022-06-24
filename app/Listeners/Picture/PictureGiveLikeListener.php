<?php

namespace App\Listeners\Picture;

use App\Events\Picture\PictureGiveLikeEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Picture\PictureGiveLikeRecord;
use App\Models\Picture\PictureTemperatureRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\Picture\PictureEvent;

class PictureGiveLikeListener
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
     * @param  PictureGiveLikeEvent  $event
     * @return void
     */
    public function handle(PictureGiveLikeEvent $event)
    {
        //  这里主要是考虑record给图片热度带来的变化
        $user = Auth::user();
        $re = $event->pictureGiveLikeRecord;
        $stand = $re->stand;
        $b_id = 0;
        // 1支持2反对3删除
        if($stand==1) {
            $b_id = 79;
        } else if($stand==2) {
            $b_id = 80;
        } else if($stand==3) {
            $b_id = 81;
        }
        PictureTemperatureRecord::recordAdd($re->picture_id,$re->user_id,$b_id,$createtime);
        $content = '用户予分';
        PictureEvent::eventAdd($link->picture_id,$user->id,$user->username,$content,$createtime);
    }
}
