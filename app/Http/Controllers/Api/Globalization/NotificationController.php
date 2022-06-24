<?php

namespace App\Http\Controllers\Api\Globalization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Globalization\GlobalNotification;
use Carbon\Carbon;

class NotificationController extends Controller
{
	public function global_notifications(Request $request) {
		$data = $request->data;
        $pageSize = $data['pageSize'];
		$ns = GlobalNotification::orderBy('updated_at','desc')->with('creator')->with('editor')->paginate($pageSize);
		return [
            'notifications'=> $ns,
        ];
	}

	// the first,通知持续时间最多为一个星期
	public function global_notification() {
		$t = Carbon::now()->subDays(7);
		$n = GlobalNotification::where('updated_at','>',$t)->orderBy('updated_at','desc')->with('creator')->with('editor')->first();
		return [
            'n'=> $n,
        ];
	}
    // 写入
    public function notificationModify(Request $request) {
    	$data = $request->data;
    	$content = $data['content'];
    	$user = auth('api')->user();
    	$new = $data['isCreate'];
    	$result = false;
    	$n = '';
    	$id = 0;
    	if($user && $content) {
    		if($new) {
    			$id = $result = GlobalNotification::newNotification($user->id,$content);
    		} else {
    			$id = $data['id'];
    			$result = GlobalNotification::modify($id,$user->id,$content);
    		}
    		
    	}
    	if($result && $id) {
    		$n = GlobalNotification::where('id',$id)->with('creator')->with('editor')->first();
    	}
    	return [
    		'success'	=>	$result?true:false,
    		'n'	=>	$n
    	];
    }
}
