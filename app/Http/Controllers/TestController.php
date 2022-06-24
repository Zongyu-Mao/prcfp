<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Events\Auth\UserRegisterdEvent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    //
    public function test() {
    	$user = User::find(1);
        
        return ['user'=>$user];
    }


    public function verify(Request $request) {
    	$user = User::find($request->user_id);
    	
        event(new UserRegisterdEvent($user));
    	return ($user);
    }
}
