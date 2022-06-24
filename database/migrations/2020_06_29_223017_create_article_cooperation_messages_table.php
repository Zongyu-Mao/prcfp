<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCooperationMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_cooperation_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cooperation_id'); //著作协作id
            $table->integer('pid') -> default(0);  //从属评论id
            $table->string('title');  //评论标题，必填
            $table->text('content');  //评论内容，必填
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
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
        Schema::dropIfExists('article_cooperation_messages');
    }
}
