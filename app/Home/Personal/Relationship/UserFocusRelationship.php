<?php

namespace App\Home\Personal\Relationship;

use Illuminate\Database\Eloquent\Model;

class UserFocusRelationship extends Model
{
    protected $fillable = ['user_id','focus_id','createtime'];
    public $timestamps = false;

    protected function focusRelationshipAdd($user_id,$focus_id,$createtime) {
        $result = UserFocusRelationship::create([
            'user_id'   => $user_id,
            'focus_id'	=> $focus_id,
            'createtime'=>$createtime,
        ]);
        return $result->id ? '1':'0';
    }
}
