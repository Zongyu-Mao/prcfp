<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;

class SanctumAuthController extends Controller
{
    public function signin(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            // 'email' => 'required|email',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);
        if(Auth::attempt(['username' => $request->username, 'password' => $request->password])){ 
            $authUser = Auth::user();
            $success['auth_token'] =  $authUser->createToken('MyAuthApp')->plainTextToken; 
            $success['username'] =  $authUser->username;
            $success['user_id'] =  $authUser->id;
   
            // return $this->sendResponse($success, 'User signed in');
            return response()->json($success, 200);
        }else if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }else{ 
            return response()->json(['error' => 'Unauthorized'], 401);
        } 
    }
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        // if($validator->fails()){
        //     return $this->sendError('Error validation', $validator->errors());       
        // }
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // $user = User::create(array_merge(
        //             $validator->validated(),
        //             ['password' => bcrypt($request->password)]
        //         ));
        // if($user)event(new UserRegisterdEvent($user));
        
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['user_id'] =  $user->id;
        $success['username'] =  $user->username;
   		return response()->json($success, 201);
    }

    public function userProperty() {
        $user = Auth::user()->only('id','username','gold','silver','copper','status','specialty');
        return $user;
    }
    // logout可能现在还不能作用
    public function logout() {
        return Auth::logout();
    }
}
