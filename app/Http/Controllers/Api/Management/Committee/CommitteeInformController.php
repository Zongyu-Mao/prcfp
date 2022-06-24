<?php

namespace App\Http\Controllers\Api\Management\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Personnel\Inform;
use App\Home\Personnel\MessageInform;
use App\Home\Personnel\JudgementInform;
use Illuminate\Support\Facades\Auth;

class CommitteeInformController extends Controller
{
    //
    public function committeeInforms(Request $request) {
    	$data = $request->data;
        $scope = $data['scope'];
        $pageSize = $data['pageSize'];
    	if($scope==1)$informs = Inform::orderBy('created_at','desc')->with('author')->with('getTarget')->with('getMedals')->with('records')->paginate($pageSize);
        if($scope==2)$informs = JudgementInform::orderBy('created_at','desc')->with('author')->with('getTarget')->with('getMedals')->with('records')->paginate($pageSize);
        if($scope==3)$informs = MessageInform::orderBy('created_at','desc')->with('author')->with('getTarget')->with('getMedals')->with('records')->paginate($pageSize);
        return $informs = [
            'informs'=> $informs,
        ];
    }
}
