<?php

namespace App\Http\Controllers\Api\Basic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryContent;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticleContent;
use App\Models\Publication\Article\ArticlePart;
use Illuminate\Support\Facades\Auth;

class BasicController extends Controller
{
    //
    public function contents(Request $request) {
    	$data = $request->data;
    	$id = $data['id'];
    	$scope = $data['scope'];
    	if($scope==1) {
    		$basic = Entry::find($id);
    		$contents = $basic->contents;
    		$references = $basic->references;
    	} else if ($scope==2) {
    		$basic = ArticlePart::find($id);
    		$contents = $basic->contents;
    		$references = $basic->references;
    	}
    	return $data = [
    		'contents'	=>	$contents,
    		'references'	=>	$references,
    	];
    }
}
