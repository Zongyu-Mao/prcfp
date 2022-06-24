<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamReviewDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_review_discussions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rid');
            $table->string('title');
            $table->text('comment');
            $table->integer('pid') -> default('0');  //从属评论id
            $table->integer('author_id');
            $table->tinyInteger('standpoint') -> default('2');//评论立场，1是支持，2是普通，3是中立
            $table->timestamp('createtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_review_discussions');
    }
}
