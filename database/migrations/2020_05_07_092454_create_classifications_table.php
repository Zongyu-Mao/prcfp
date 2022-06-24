<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classname');//分类名
            $table->integer('pid');//父分类
            $table->tinyInteger('level');//层级，目前先来五级，防止后面可能出现自定义分类或者啥啥，肯定还有一级分类
            $table->integer('creator_id');//创建者id
            $table->string('creator',20);//创建者
            $table->integer('revisor_id')->nullable();//更新者id
            $table->string('revisor',20)->nullable();//更新者
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
        Schema::dropIfExists('classifications');
    }
}
