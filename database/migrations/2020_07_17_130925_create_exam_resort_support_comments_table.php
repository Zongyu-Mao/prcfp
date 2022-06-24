<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamResortSupportCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_resort_support_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');
            $table->integer('resortId');
            $table->string('title');
            $table->text('comment');
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id'); 
            $table->integer('stars') -> default(0);; 
            $table->tinyInteger('type') -> default(0);//类型：0是正常的普通回复1是接受或者反对帮助类型
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
        Schema::dropIfExists('exam_resort_support_comments');
    }
}
