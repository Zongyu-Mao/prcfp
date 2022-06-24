<?php

namespace App\Http\Controllers\Api\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Auth;

class LoginController extends Controller
{
    //use AuthenticatesUsers;

    // 验证前端登录数据
    public function login(Request $request)
    {
    	// 此处直接调用了auth方法，打算还是先使用laravel自带的后台验证方法不做改变
    	if($request->username && $request->password) {
    		$attempt = Auth::attempt(['username' => $request->username, 'password' => $request->password]);
    		// $attempt = Auth::attempt(['username' => $request->username, 'password' => Crypt::decrypt($request->password)]);
    	}
    	// 验证成功返回数据（这里返回字符串有bug）
    	if($attempt) {
    		$result = '1';
    	}else{
    		$result = '0';
    	}

    	// return $result;
    	return $result;

        // $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        // if ($this->hasTooManyLoginAttempts($request)) {
        //     $this->fireLockoutEvent($request);

        //     return $this->sendLockoutResponse($request);
        // }

        // if ($this->attemptLogin($request)) {
        //     return $this->sendLoginResponse($request);
        // }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        // $this->incrementLoginAttempts($request);

        // return $this->sendFailedLoginResponse($request);
    }


}
