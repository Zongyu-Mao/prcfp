<?php

namespace App\Listeners\Auth;

use App\Events\Auth\UserRegisterdEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Mail\SendVerifyMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Home\Personnel\Role;
use App\Home\Personnel\UserRole;
use App\Home\Personal\Level\UserLevel;
use App\Home\Personal\Prop\UserProp;
use App\Home\Personal\Prop;

class UserRegisterdListener
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
     * @param  UserRegisterdEvent  $event
     * @return void
     */
    public function handle(UserRegisterdEvent $event)
    {
        //用户注册后，基本身份，发送通知，发送邮件？
        $user = $event->user;
        $result = false;
        $email = $user->email;
        if(is_null($user->email_verified_at) && $email){
            $isOtherToken = DB::table('verify_emails')->where('email', $email)->first();
            if($isOtherToken) {
                $token = $isOtherToken->token;
            } else {
                $token = Str::random(80);
                DB::table('verify_emails')->insert([
                    'email' => $email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]);
            }
            $result = Mail::to($email)->send(new SendVerifyMail($token));
        }

        // 1赋予基本角色
        // $role_id = Role::where('type',1)->first()->id;
        // UserRole::roleCreate($user->id,$role_id,Carbon::now());
        // // 新用户道具初始化
        // UserProp::propInitialization($user->id);
        // // 等级初始化
        // $level_id = Level::where('sort',1)->first()->id;
        // UserLevel::levelInitialization($user->id,$user->grow_value);
        // 发送新手通知
        
    }
}
