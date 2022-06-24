<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamPicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id'); //词条id
            $table->string('title');    //图片标题
            $table->string('source');   //图片来源
            $table->string('url');   //图片的存放地址
            $table->integer('author'); //图片的上传者
            $table->integer('likes')->default(0); //喜欢
            $table->integer('unlikes')->default(0); //不喜欢
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
        Schema::dropIfExists('exam_pictures');
    }
}
