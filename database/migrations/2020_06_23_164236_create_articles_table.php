<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table -> Integer('cid');     //所属内容分类id
            $table -> string('title') -> unique();       //著作标题
            $table -> string('etitle');
            $table -> text('summary') -> nullable();
            $table -> tinyInteger('nature') -> default('0');    //著作性质，0经典1新作
            $table -> tinyInteger('level') -> default('1');    //著作等级
            $table -> tinyInteger('surveillance',1) -> default(0); //巡查状况
            $table -> Integer('cooperation_id') -> nullable(); //当前协作计划id
            $table -> Integer('review_id') -> nullable(); //当前评审id
            $table -> Integer('cover_id') -> default('1');      //封面图片id
            $table -> tinyInteger('status') -> default('0');      //文章状态,0协作1开放2锁定3关闭
            $table -> Integer('manage_id') -> default('1');    //自管理员id
            $table -> Integer('creator_id');    //创建者id
            $table -> Integer('lasteditor_id');      //最后编辑者
            $table -> Integer('stars') -> default('0');
            $table -> Integer('shares') -> default('0');
            $table -> Integer('edit_number') -> default('1');
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
        Schema::dropIfExists('articles');
    }
}
