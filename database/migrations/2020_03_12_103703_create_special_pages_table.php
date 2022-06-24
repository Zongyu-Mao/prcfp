<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_pages', function (Blueprint $table) {
            $table->increments('id');
            $table -> string('title') -> unique();       //词条标题
            $table -> text('summary') -> nullable();
            $table -> text('content') -> nullable(); //正文内容id
            $table -> enum('type',[0,1,2,3]) -> default('0');      //文章的类型，0是网站的基本介绍1是网站的规则包括书写等的格式2是一些常见的内容
            $table -> Integer('manager_id');    //自管理员id
            $table -> Integer('lasteditor_id');      //最后编辑者
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
        Schema::dropIfExists('special_pages');
    }
}
