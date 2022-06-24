<?php

namespace App\Http\Controllers\Api\Picture\PictureDelete;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Storage;
use Illuminate\Support\Facades\Auth;

class PictureDeleteController extends Controller
{
    //
    public function pictureDeleteOnUnexpectedDestroy(Request $request) {
    	$path = $request['path'];
    	// return $request;
    	$result = 0;
    	if(count($path)>0){
			for($i=0;$i<count($path);$i++){
				Storage::disk('public')->delete($path[$i]);
				$result ++ ;
			}
		}
		return  [
			'success'=>$result?true:false,
			'result'=>$result
		];
    }
}
