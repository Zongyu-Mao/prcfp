<?php

namespace App\Home\Encyclopedia\EntryReview;

use Illuminate\Database\Eloquent\Model;

class EntryReviewRecord extends Model
{
	protected $fillable = ['review_id','user_id','username','standpoint','createtime'];

	public $timestamps = false;

     // 获取同意的票数
     protected function getAgreeNum($id){
          return $result = EntryReviewRecord::where([['review_id',$id],['standpoint',1]])->count();
     }

     // 获取反对的票数
     protected function getOpposeNum($id){
          return $result = EntryReviewRecord::where([['review_id',$id],['standpoint',2]])->count();
     }

     // 获取中立的票数
     protected function getNeutralNum($id){
          return $result = EntryReviewRecord::where([['review_id',$id],['standpoint',3]])->count();
     }

    //添加评审记录
     protected function reviewRecordAdd($reviewId,$userId,$username,$standpoint,$createtime){
     	$result = EntryReviewRecord::create([
     		'review_id'	=>$reviewId,
               'user_id' =>$userId,
     		'username'=>$username,
     		'standpoint'=>$standpoint,
     		'createtime'=>$createtime
     	]);
     	return $result;
     }
}
