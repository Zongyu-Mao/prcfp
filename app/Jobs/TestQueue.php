<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Home\UserDynamic;
use App\User;
use Carbon\Carbon;

class TestQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = $this->user;
        $user_id = $user->id;
        $username = $user->username;
        $behavior = '这是一个redis的队列测试内容添加';
        $objectName = 'redis的队列测试';
        $objectURL = '/home';
        $fromName = 'PerfectReference';
        $fromURL = '/home';
        $createtime = Carbon::now();
        UserDynamic::dynamicAdd($user_id,$username,$behavior,$objectName,$objectURL,$fromName,$fromURL,$createtime);
    }
}
