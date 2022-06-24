<?php

namespace App\Http\Controllers\Api\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Http\Requests\Api\Auth\RegisterAuthRequest;
use JWTAuth;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTReception;

class RegisterController extends Controller
{
	// public function __construct()
 //    {
 //        $this->middleware('auth.jwt', ['except' => ['login','register']]);
 //    }

	public $loginAfterSignUp = true;

    public function register(RegisterAuthRequest $request)
    {
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        if ($this->loginAfterSignUp) {
            return $this->login($request);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
    	// 原文
        // $input = $request->only('email', 'password');
        //  修改
        $input = $request->only('username', 'password');
        $jwt_token = null;

        // if (!$jwt_token = JWTAuth::attempt($input)) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Invalid Email/Username or Password(错误的账户名或密码)',
        //     ], 401);
        // }

        if (!$jwt_token = JWTAuth::attempt($input)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Email/Username or Password(错误的账户名或密码)',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'token' => $jwt_token,
            'user' => JWTAuth::user()
        ]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, the user cannot be logged out'
            ], 500);
        }
    }

     /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    //
    // public function register(Request $request)
    // {
    // 	return 1234;
    // }

 
}
