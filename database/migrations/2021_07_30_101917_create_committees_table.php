<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table -> string('title') -> unique();       //标题
            $table -> Integer('tcid');     //顶层分类
            $table -> Integer('scid');     //所属分类
            $table -> Integer('thcid');     //所属分类
            $table -> Integer('cid');     //所属分类
            $table -> tinyInteger('hierarchy');     //层级，对应cid
            $table -> Integer('emblem');    //组织徽章
            $table -> text('introduction') -> nullable();   //组织介绍
            $table -> Integer('manage_id') -> default(0);    //管理员id
            $table -> Integer('creator_id');    //创建者id
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
        Schema::dropIfExists('committees');
    }
}
