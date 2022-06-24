<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aid'); //著作id
            $table->tinyInteger('target') -> default(4); //评审目标
            $table->integer('cid') -> default(0);
            $table->date('deadline');  //截止时间
            $table->string('title');  //评审内容标题
            $table->text('content');  //评审内容描述
            $table->integer('initiate_id');//发起人id
            $table->string('initiater',20);//发起人
            $table->integer('party_num') -> default(0);//参与人数
            $table->integer('agree_num') -> default(0);//同意人数
            $table->integer('oppose_num') -> default(0);//反对人数
            $table->integer('neutrality_num') -> default(0);//中立人数
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
        Schema::dropIfExists('article_reviews');
    }
}
