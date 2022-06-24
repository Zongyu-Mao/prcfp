<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id'); //试卷id
            $table->tinyInteger('target') -> default(4); //评审目标
            $table->integer('cid') -> default(0);
            $table->date('deadline');  //截止时间
            $table->string('title');  //评审内容标题
            $table->text('content');  //评审内容描述
            $table->integer('initiate_id');//发起人id
            $table->string('initiater',20);//发起人
            $table->tinyInteger('status') -> default(0);//评审状态，0在审1通过2未通过
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
        Schema::dropIfExists('exam_reviews');
    }
}
