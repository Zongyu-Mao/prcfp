<?php

namespace App\Http\Controllers\Api\Personnel\Inform;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\MedalSuit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PunishSuitController extends Controller
{
    public function punishSuit(Request $request) {
    	$suits = MedalSuit::where('type',2)->with('getMedals')->get();
    	$user = auth('api')->user()->only('id','username','level','gold','silver','copper','status','exp_value','grow_value');
    	return ['suits'=>$suits,'user'=>$user];
    }
}
