<?php

namespace App\Http\Controllers\Api\Personal;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PersonalNotificationController extends Controller
{
	public function notification(Request $request){
        $user = auth('api')->user();
        $pageSize = $request->pageSize;
        $notifications = $user->notifications()->paginate($pageSize);
        $user->unreadNotifications->markAsRead();
        return $data = array(
            'notifications' => $notifications,
            'pageSize' => $pageSize
        );
    }

    // 通知的删除
    public function personalNotificationDelete(Request $request){
        $id = $request->get('id');

    }

}
