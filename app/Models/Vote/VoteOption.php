<?php

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteOption extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['vid','choice_option','createtime'];

    // 写入选项,是一个一个写入的
    protected function optionCreate($vid,$choice_option,$createtime) {
        $result = VoteOption::create([
            'vid'       => $vid,
            'choice_option'     => $choice_option,
            'createtime'    => $createtime
        ]);
        // 写入选项暂时不考虑触发事件
        return $result->id;
    }
}
