<?php

namespace App\Http\Controllers\Api\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class RegisterCheckController extends Controller
{
    //
    public function emailCheck(Request $request) {
    	$email = $request->email;
    	$result=0;
    	if(User::where('email',$email)->count()){
    		$result=1;
    	}
    	
    	return $result;
    }

    public function usernameCheck(Request $request) {
    	$username = $request->username;
    	$result=0;
    	if(User::where('username',$username)->count()){
    		$result=1;
    	}
    	
    	return $result;
    }
}
