<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\TypeModyfiedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Committee\Surveillance\Mark\MarkTypeEvent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TypeModyfiedListener
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
     * @param  TypeModyfiedEvent  $event
     * @return void
     */
    public function handle(TypeModyfiedEvent $event)
    {
        $type = $event->surveillanceMarkType;
        $user = auth('api')->user();
        $createtime = Carbon::now();
        MarkTypeEvent::typeEventAdd($type->id,$user->id,$user->username,'修改了主内容标记类型['.$type->sort.']。',$createtime);
    }
}
