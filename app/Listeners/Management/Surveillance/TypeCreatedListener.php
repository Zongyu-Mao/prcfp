<?php

namespace App\Listeners\Management\Surveillance;

use App\Events\Management\Surveillance\TypeCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Committee\Surveillance\Mark\MarkTypeEvent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TypeCreatedListener
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
     * @param  TypeCreatedEvent  $event
     * @return void
     */
    public function handle(TypeCreatedEvent $event)
    {
        //标记type创建，目前仅需放在管理组消息中
        $type = $event->surveillanceMarkType;
        $user = auth('api')->user();
        $createtime = Carbon::now();
        MarkTypeEvent::typeEventAdd($type->id,$user->id,$user->username,'新建了主内容标记类型['.$type->sort.']。',$createtime);
    }
}
