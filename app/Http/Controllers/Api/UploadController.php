<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Home\Personal\UserPicture;
use App\Home\Encyclopedia\Entry;
use App\Home\Encyclopedia\Entry\EntryPicture;
use App\Home\Publication\Article;
use App\Home\Publication\Article\ArticlePicture;
use App\Home\Examination\Exam;
use App\Home\Examination\Exam\ExamPicture;
use App\Home\Organization\Group;
use App\Home\Organization\Group\GroupEmblem;
use App\Models\User;
use Storage;

class UploadController extends Controller
{
    //接收前台的图片
    public function upload(Request $request,$scope,$modify_id=0) {
        $filename = '';
    	$path = '';
        $user = Auth::user();
        if ($request -> hasFile('file') && $request -> file('file') -> isvalid()) { //判断文件上传是否有效

        	$filename = sha1(time() . $request -> file('file') -> getClientOriginalName()) . '.' . $request -> file('file') -> getClientOriginalExtension();
    		//文件保存
    		if($scope==1){
                $path = $request -> file('file')->storeAs(
                    'avatars/entry',$filename, 'public'
                );
                if($modify_id>0) {
                    $avatar = Entry::find($modify_id)->avatar;
                    if($avatar) {
                        Storage::disk('public')->delete($avatar->url);
                        if(!EntryPicture::avatarUpdate($avatar->id,$path)){
                            Storage::disk('public')->delete($path);
                            $path = '';
                        }
                    } else {
                        $avatar_id = EntryPicture::entryPictureAdd($modify_id,'avatar','avatar',$paths,$user->id,0,0);
                        Entry::avatarUpdate($modify_id,$avatar_id);
                    }
                    
                }
                // Storage::disk('public') -> put($filename,file_get_contents($request -> file('file') ->path()));
            }elseif($scope==2){
                $path = $request -> file('file')->storeAs(
                    'avatars/article',$filename, 'public'
                );
                if($modify_id>0) {
                    $avatar = Article::find($modify_id)->avatar;
                    if($avatar) {
                        Storage::disk('public')->delete($avatar->url);
                        if(!ArticlePicture::avatarUpdate($avatar->id,$path)){
                            Storage::disk('public')->delete($path);
                            $path = '';
                        }
                    } else {
                        $avatar_id = ArticlePicture::articlePictureAdd($modify_id,'avatar','avatar',$paths,$user->id);
                        Article::avatarUpdate($modify_id,$avatar_id);
                    }
                    
                }
            }elseif($scope==3){
                $path = $request -> file('file')->storeAs(
                    'avatars/exam',$filename, 'public'
                );
                if($modify_id>0) {
                    $avatar = Exam::find($modify_id)->avatar;
                    if($avatar) {
                        Storage::disk('public')->delete($avatar->url);
                        if(!ExamPicture::avatarUpdate($avatar->id,$path)){
                            Storage::disk('public')->delete($path);
                            $path = '';
                        }
                    }else {
                        $avatar_id = ExamPicture::examPictureAdd($modify_id, $path);
                        Exam::avatarUpdate($modify_id,$avatar_id);
                    }
                    
                }
            }elseif($scope==4){
                $path = $request -> file('file')->storeAs(
                    'avatars/group',$filename, 'public'
                );
                if($modify_id>0) {
                    $avatar = Group::find($modify_id)->avatar;
                    if($avatar) {
                        if($avatar->url)Storage::disk('public')->delete($avatar->url);
                        if(!GroupEmblem::emblemUpdate($avatar->id,$path)){
                            Storage::disk('public')->delete($path);
                            $path = '';
                        }
                    } else {
                        $avatar_id = GroupEmblem::emblemAdd($modify_id, $path);
                        Group::avatarUpdate($modify_id,$avatar_id);
                    }
                    
                }
            }elseif($scope==10){
                // 头像直接在这里完成处理，上传后写入数据表，并删除原来的旧头像，注意处理图片大小还没做
                $path = $request -> file('file')->storeAs(
                    'avatars/user',$filename, 'public'
                );
                $result = false;
                $user = auth('api')->user();
                if($user){
                    $avatarId = UserPicture::avatarAdd($user->id,$path);
                    $result = User::where('id',$user->id)->update([
                        'avatar'    => $avatarId
                    ]);
                    if($delpath = $user->getAvatar->url){
                        Storage::disk('public')->delete($delpath);
                    }
                }
                if(!$result){
                    Storage::disk('public')->delete($path);
                    $path = '';
                }
            }elseif($scope==9){
                $path = $request -> file('file')->storeAs(
                    'avatars/committee',$filename, 'public'
                );
            }
    		
        }
        return $path;
    }
}
