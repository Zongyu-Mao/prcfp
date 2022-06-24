<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_question_options', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qid'); //所属的题目id
            $table->text('option');  //选项内容
            $table->tinyInteger('sort'); //选项排序       
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
        Schema::dropIfExists('exam_question_options');
    }
}
