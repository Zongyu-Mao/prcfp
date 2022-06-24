<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleCooperationVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_cooperation_votes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cooperation_id');//著作协作计划id
            $table->integer('aid');//著作id
            $table->tinyInteger('type') -> default(1);//投票类型，1自定义协作事务2申请进组3弹劾组长4劝退组员
            $table->tinyInteger('timelimit') -> default(3);//投票的周期，1天2天3天
            $table->date('deadline');  //截止时间
            $table->integer('initiate_id');//发起人id
            $table->string('initiate',20);//发起人
            $table->string('title');//投票标题
            $table->text('content');//投票内容说明
            $table->tinyInteger('status') -> default(0);//投票状态，0过程1通过2不通过
            $table->string('remark')->nullable();//投票信息备注
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
        Schema::dropIfExists('article_cooperation_votes');
    }
}
