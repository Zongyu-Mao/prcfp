<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamReviewOpponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_review_opponents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rid'); //词条评审id
            $table->string('title');  //反对评论标题，必填
            $table->text('comment');  //反对评论内容，必填
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
            $table->integer('recipient_id') -> nullable();  //接收人id
            $table->string('recipient',20) -> nullable();  //接收人
            $table->integer('stars') -> default(0);   //星星（支持）数
            $table->tinyInteger('round') -> default(1);//轮次，默认第一轮，不能超过三轮是否需要？？？？？？？？？？？？？
            $table->tinyInteger('status') -> default(0);//状态，0在过程中，1被接受，2不被接受（下一轮）3过期4转辩论5失效
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
        Schema::dropIfExists('exam_review_opponents');
    }
}
