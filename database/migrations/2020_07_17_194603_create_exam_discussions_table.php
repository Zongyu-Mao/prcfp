<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_discussions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id'); //试卷id
            // $table->date('deadline');  //截止时间，普通讨论内容无需此项限制
            $table->string('title');  //建议评论标题，必填
            $table->text('comment');  //建议评论内容，必填
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
            $table->integer('recipient_id') -> nullable();  //接收人id
            $table->string('recipient',20) -> nullable();  //接收人
            $table->tinyInteger('round') -> default(1);//轮次，默认第一轮，不能超过四轮，这里可能要商量一下
            $table->tinyInteger('status') -> default(0);//状态，0在过程中，1被接受，2不被接受（下一轮）3过期4失效（被举报）
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
        Schema::dropIfExists('exam_discussions');
    }
}
