<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Events\Auth\UserRegisterdEvent;
use App\Models\User;
use Carbon\Carbon;

class EmailVerifiedController extends Controller
{
    //确认邮箱后，需要真正生成用户资料，加入角色、等级、状态、道具等。
    public function emailVerifiedHandle(Request $request){
    	$verify = DB::table('verify_emails')->where('token',$request->token)->first();
    	if($verify && $email = $verify->email){
    		return $this->emailVerified($email);
    	}else {
    		return $this->tokenNotFoundError();
    	}
    }

    // Token not found response
    private function tokenNotFoundError() {
        return response()->json([
          'error' => 'Either your email or token is wrong.'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Reset password
    private function emailVerified($email) {
        // find email
        $userData = User::whereEmail($email)->first();
        // update user data
        if($userData->status==0){
        	$userData->update([
	          'email_verified_at' => Carbon::now(),
	          'level'=>1,
	          'gold'=>1,
	          'silver'=>2,
	          'copper'=>5,
	          'status'=>1,
	        ]);
        }
        
        // remove verification data from db
        DB::table('verify_emails')->where('email',$email)->delete();
        // reset password response
        return response()->json([
          'data'=>'User data has been updated.<br>[您的账户已经验证，资料已经更新。]',
          'user'=>User::find($userData->id)
        ],Response::HTTP_CREATED);
    }

    public function verifyConfirm(Request $request) {
    	$user_id = $request->user_id;
    	$user = User::find($user_id);
    	$input_email = $request->email;
    	$email = $user->email;
    	$result = false;
    	$msg = '';
    	if($input_email==$email && $user->status==0){
    		event(new UserRegisterdEvent($user));
    	    $msg = '成功发送验证邮件。';
    	}
    	return ['message'=>$msg, 'user'=>$user];
    }

}
