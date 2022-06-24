<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table -> Integer('cid');     //所属内容分类id
            $table -> string('title') -> unique();       //标题
            $table -> string('etitle');
            $table -> text('summary') -> nullable();
            $table -> tinyInteger('nature',1) -> default(1);    //著作性质，1经典2新作
            $table -> tinyInteger('level',1) -> default(1);    //等级
            $table -> Integer('difficulty') -> default(1);    //难度
            $table -> tinyInteger('total') -> default(0);    //总分
            $table -> tinyInteger('score_avg') -> default(0);    //平均分
            $table -> tinyInteger('stand',1) -> default(1); //试卷的多选评分标准，1少选得分，2不得分
            $table -> tinyInteger('surveillance',1) -> default(0); //巡查状况
            $table -> Integer('cooperation_id') -> nullable(); //当前协作计划id
            $table -> Integer('cover_id') -> nullable(); //当前评审id
            $table -> tinyInteger('status',1) -> default(1);      //文章状态,1协作2开放3锁定4关闭
            $table -> Integer('manage_id') -> default(1);    //自管理员id
            $table -> Integer('creator_id');    //创建者id
            $table -> Integer('lasteditor_id');      //最后编辑者
            $table -> Integer('stars') -> default(0);
            $table -> Integer('shares') -> default(0);
            $table -> Integer('edit_number') -> default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
