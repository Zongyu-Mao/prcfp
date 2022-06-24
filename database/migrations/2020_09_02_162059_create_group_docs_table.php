<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_docs', function (Blueprint $table) {
            $table->increments('id');
            $table -> Integer('gid');     //所属组织id
            $table -> string('title');       //标题
            $table -> text('summary') -> nullable();    //摘要
            $table -> text('content') -> nullable();    //正文内容
            $table -> tinyInteger('type') -> default('1');      //文章类型
            $table -> tinyInteger('status') -> default('0');      //文章状态,0正常1封禁2删除
            $table -> Integer('creator_id');    //创建者id
            $table -> string('creator');    //创建者
            $table -> Integer('views') -> default('1');
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
        Schema::dropIfExists('group_docs');
    }
}
