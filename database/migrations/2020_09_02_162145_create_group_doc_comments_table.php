<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDocCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_doc_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('did'); //试卷id
            $table->string('title');  //评论标题，必填
            $table->text('comment');  //评论内容，必填
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id');  //作者id
            $table->string('author',20);  //作者
            $table->tinyInteger('status') -> default(0);//状态，0正常，1限制编辑，2失效封禁（被举报）
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
        Schema::dropIfExists('group_doc_comments');
    }
}
