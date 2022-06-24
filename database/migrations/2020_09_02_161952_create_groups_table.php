<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table -> Integer('cid');     //所属内容分类id
            $table -> string('title');       //组织成名
            $table -> Integer('emblem') -> default('0');    //组织徽章
            $table -> text('introduction') -> nullable();   //组织介绍
            $table -> tinyInteger('ifSeeking') -> default('1');    //期望的加入用户
            $table -> tinyInteger('level') -> default('1');    //组织等级，假设5级
            $table -> tinyInteger('status') -> default('0');      //组织状态,0正常1封禁
            $table -> Integer('manage_id') -> default('1');    //管理员id
            $table -> string('manager');
            $table -> Integer('creator_id');    //创建者id
            $table -> string('creator');
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
        Schema::dropIfExists('groups');
    }
}
