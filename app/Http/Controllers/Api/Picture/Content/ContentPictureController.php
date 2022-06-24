<?php

namespace App\Http\Controllers\Api\Picture\Content;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;
use App\Home\Encyclopedia\Entry;

class ContentPictureController extends Controller
{
    //
    public function contentPictures(Request $request) {
    	$data = $request->data;
    	$scope = $data['scope'];
    	$id = $data['id'];
        $disk = Storage::disk('local');
        $files = '';
        $directory = '';
    	if($scope==1) {
    		$disk = Storage::disk('local');
    		$directory = 'public/entries/entry'.$id.'/content';
    	} else if($scope==2) {
            $disk = Storage::disk('local');
            $directory = 'public/articles/article'.$id.'/content';
        } else if($scope==3) {
            $directory = 'public/exams/exam'.$id.'/content';
        }
        if($directory)$files = $disk->allFiles($directory);
    	return ['files'=>$files, 'd'=>$disk];
    }

    public function linkPictures(Request $request) {
        // link只针对scope=1
    	$data = $request->data;
    	$scope = $data['scope'];
    	$id = $data['id'];
    	if($scope==1) {
    		$links = Entry::where('id',$id)->first()->links;
    	}
    	return ['links'=>$links];
    }

    public function pictureDelete(Request $request) {
    	$data = $request->data;
    	$path = $data['path'];
    	$scope = $data['scope'];
    	$id = $data['id'];
    	$result = Storage::disk('public')->delete($path);
    	return ['success'=>$result?true:false];
    }
}
