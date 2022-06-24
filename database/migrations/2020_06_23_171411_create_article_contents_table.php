<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('aid'); //所属著作id
            $table->bigInteger('part_id'); //所属章回id
            $table->integer('sort'); //在著作中的排序，不同于词条的新增内容
            $table->tinyInteger('lock'); //本章的锁定状态0不锁定无编辑，1锁定正在编辑
            $table->text('content');  //对应的著作正文内容
            $table->bigInteger('editor_id'); //如果正在编辑，代表编辑者id，如果无编辑，代表最后编辑者
            $table->string('ip',15); //编辑者ip
            $table->tinyInteger('big')->default(0);  //是否为大改动,创建时系统带入，其余用户选择0小1大
            $table->string('reason');  //改动的原因，创建时系统带入，其余用户输入
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
        Schema::dropIfExists('article_contents');
    }
}
