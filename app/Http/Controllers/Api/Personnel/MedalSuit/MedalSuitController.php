<?php

namespace App\Http\Controllers\Api\Personnel\MedalSuit;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Home\Personnel\MedalSuit;

class MedalSuitController extends Controller
{
    public function medalSuitDetail(Request $request) {
    	$id = $request->id;
    	$title = $request->title;
    	return $suit = MedalSuit::where('id',$id)->with('getMedals')->first();
    }
}
