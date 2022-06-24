<?php

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document\DocumentDirectory;
use App\Models\Document\DocumentDirectoryEvent;
use Carbon\Carbon;

class DocumentDirectoryController extends Controller
{
    //
    public function directories(Request $request) {
    	$directories = DocumentDirectory::where('pid','0')->with('allDirectories')->get();
    	return array(
    		'directories' => $directories
    	);
    }

    public function directoryCreate(Request $request) {
    	$data = $request->data;
    	$classname = $data['classname'];
    	$pid = $data['dirA_id'];
    	$level = $data['level'];
    	$user_id = $data['user_id'];
    	$result = 0;
        $ds = '';
    	if(($level==1 && $pid==0) || ($level==2 && $pid!=0)){
    		$result = DocumentDirectory::addDirectory($classname,$pid,$level,$user_id);
            DocumentDirectoryEvent::directoryEventAdd($result,$user_id,auth('api')->user()->username,'创建了第'.$level.'级文档目录['.$classname.']。',Carbon::now());
    	}
        if($result)$ds = DocumentDirectory::where('pid','0')->with('allDirectories')->get();
    	return ['success'=>$result ? true: false, 'directories'=>$ds];
    }

    public function directoryModify(Request $request) {
    	$data = $request->data;
        $classname = $data['classname'];
        $modify_id = $data['modify_id'];
        $level = $data['level'];
        $user_id = $data['user_id'];
        $result = 0;
        $ds = '';
        if($modify_id && $classname){
            $result = DocumentDirectory:: modifyDirectory($classname,$modify_id,$user_id);
            $result = DocumentDirectoryEvent::directoryEventAdd($result,$user_id,auth('api')->user()->username,'修改了第'.$level.'级文档目录['.$classname.']。',Carbon::now());
        }
        if($result)$ds = DocumentDirectory::where('pid','0')->with('allDirectories')->get();
        return ['success'=>$result ? true: false, 'directories'=>$ds];
    }
}
