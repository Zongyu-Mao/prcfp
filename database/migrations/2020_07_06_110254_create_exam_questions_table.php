<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id'); //所属试卷id
            $table->tinyInteger('score'); //分值
            $table->tinyInteger('type'); //类型0单选1多选////2判断3填空4问答
            $table->integer('partStem')->default(0); //分区id
            $table->text('stem');  //题干内容
            $table->integer('sort'); //排序（第几题）
            $table->tinyInteger('options'); //选项数单选4多选4或6判断2
            $table->string('answer'); //答案
            $table->text('annotation'); //注释
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
        Schema::dropIfExists('exam_questions');
    }
}
