<?php

namespace App\Http\Controllers\Api\Assistant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Encyclopedia\Ambiguity\Polysemant;
use App\Models\Encyclopedia\Ambiguity\Synonym;
use App\Home\Encyclopedia\Entry;

class SynonymController extends Controller
{
    //
    public function synonymModify(Request $request) {
        // 多义与好友类似 是双向的，但是数据库仅记录一次
        $data = $request->data;
        $result = '';
        $backContent = '';
        if($data['clear']) {
            // 这里id是主词id，因此model只需要按照此顺序删除即可，不
            $result = Synonym::clearSynonym($data['did'],$data['obj_id']);
        }
        if($result) {
            $b = Entry::find($data['id']);
            if($b->status<5){
                if(Synonym::where('sid',$data['id'])->exists()){
                    $sids = Synonym::where('eid',$data['id'])->pluck('sid')->toArray();
                    $backContent = Entry::whereIn('id',$sids)->get(['id','title','etitle']);
                }
            }else if($b->status==5){
                $eid = Synonym::where('sid',$data['id'])->first()->eid;
                $sids = array_diff(Synonym::where('eid',$eid)->pluck('sid')->toArray(),[$data['id']]);
                if($data['id']!=$eid)array_push($sids,$eid);
                $b = Entry::where('id',$eid)->with('entryAvatar')->first();
                $backContent = Entry::whereIn('id',$sids)->get(['id','title','etitle']);
            }
        }
        return $res = [
            'success'=>$result?true:false,
            'basic'=>$b,
            'backContent'=>$backContent,
        ];
    }
}
