<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Keyword;
use DB;

class WordbankController extends Controller
{
    //得到keywords
    public function keywords(Request $request) {
    	$data = $request->data;
        // $scope = $data['scope'];
        $pageSize = $data['pageSize'];
        $keywords = Keyword::orderBy(DB::raw('convert(`keyword` using gbk)'))->paginate($pageSize);
        return $keywords = [
        	'keywords' => $keywords
        ];
    }
}
