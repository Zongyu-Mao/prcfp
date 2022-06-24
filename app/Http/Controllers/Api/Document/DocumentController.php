<?php

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document\Document;
use App\Models\Document\DocumentDirectory;
use App\Models\Document\DocumentEvent;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Home\Announcement;

class DocumentController extends Controller
{
    //
    public function documents(Request $request) {
        if(Document::where('id','>',0)->exists()) {
            $documents = Document::all();
        }else {
            $documents = '';
        }
        $directories = DocumentDirectory::where('pid','0')->with('allDirectories')->get();
        $announcements =  Announcement::where('scope',7)->orderBy('createtime','desc')->limit(10)->get();
        return array(
            'documents' => $documents,
            'announcements' => $announcements,
            'directories' => $directories
        );
    }

    public function getDocument(Request $request) {
        $id = $request->id;
        $title=$request->title;
        $document = Document::find($id);
        $path = DocumentDirectory::getPath($document->cid);
        return array(
            'document' => $document,
            'path' => $path
        );
    }
    public function getDocumentContentModifyKey(Request $request) {
        $id = $request->id;
        $change = $request->change;
        $result = false;
        $user_id = auth('api')->user()->id;
        // Redis::set('entryContentModifyKey:'.$id,0);
        $key = Redis::get('documentContentModifyKey:'.$id);
        // 设置过期时间是8小时
        if(!$key)$result = Redis::setex('documentContentModifyKey:'.$id,28800,$user_id);
        if($key==$user_id)$result = true;
        return ['success'=>$result];
    }
    public function releaseDocumentContentModifyKey(Request $request) {
        $id = $request->id;
        $user_id = auth('api')->user()->id;
        $result = false;
        if(Redis::get('documentContentModifyKey:'.$id)==$user_id){
            // 如果确实被锁定了，释放
            $result = Redis::set('documentContentModifyKey:'.$id,0);
        }
        return ['success'=>$result ? true:false];
    }

    public function documentCreate(Request $request) {
    	$data = $request->data;
        $title = $data['title'];
        $content = $data['content'];
        $cid = $data['directory_id'];
        $user_id = $data['user_id'];
        $result = 0;
        if($title && $content && $cid){
            $result = Document::documentCreate($cid,$title,$content,$user_id,$user_id);
            DocumentEvent::documentEventAdd($result,$user_id,auth('api')->user()->username,'创建了文档《'.$title.'》。',Carbon::now());
        }

        return [
            'success'=>$result ? true: false,
            'id' =>$result,
            'title' =>$title
        ];
    }

    public function documentModify(Request $request) {
    	$data = $request->data;
        $id = $data['id'];
        $title = $data['title'];
        $content = $data['content'];
        $user_id = $data['user_id'];
        $result = 0;
        if($content){
            // 这里没有释放锁，前端加了释放锁
            $result = Document::documentModify($id,$content,$user_id);
            $result = DocumentEvent::documentEventAdd($id,$user_id,auth('api')->user()->username,'修改了文档《'.$title.'》。',Carbon::now());
        }
        return ['success'=>$result ? true: false];
    }

    
}
