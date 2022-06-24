<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryPicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_pictures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eid'); //词条id
            $table->string('title');    //图片标题
            $table->string('source');   //图片来源
            $table->string('url');   //图片的存放地址
            $table->integer('author'); //图片的上传者
            $table->integer('like')->default(0); //喜欢
            $table->integer('unlike')->default(0); //不喜欢
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
        Schema::dropIfExists('entry_pictures');
    }
}
