<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamPartStemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_part_stems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id'); //试卷id，由于创建材料时qid不一定真实存在，所以添加exam_id
            $table->tinyInteger('sort'); //stem的排序
            $table->Integer('qid'); //最近的试卷题目id
            $table->tinyInteger('questions'); //该材料包含的题目数
            $table->text('content');  //材料内容
            $table->tinyInteger('lock'); //本章的锁定状态0不锁定无编辑，1锁定正在编辑       
            $table->integer('creator_id'); //创建者id
            $table->integer('editor_id'); //如果正在编辑，代表编辑者id，如果无编辑，代表最后编辑者
            $table->string('ip',15); //编辑者ip
            $table->tinyInteger('big')->default(0);  //是否为大改动,创建时系统带入，其余用户选择0小1大
            $table->string('reason');  //改动的原因，创建时系统带入，其余用户输入
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
        Schema::dropIfExists('exam_part_stems');
    }
}
