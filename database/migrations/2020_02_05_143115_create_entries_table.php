<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entries', function (Blueprint $table) {
            $table -> increments('id');
            $table -> integer('cid');     //所属内容分类id
            $table -> string('title') -> index();       //词条标题
            $table -> string('etitle');
            $table -> text('summary') -> nullable();
            $table -> tinyInteger('level') -> default('1');    //词条等级
            $table -> tinyInteger('surveillance',1) -> default(0); //巡查状况
            $table -> Integer('cooperation_id') -> nullable(); //当前协作计划id
            $table -> Integer('review_id') -> nullable(); //当前评审id
            $table -> Integer('cover_id') -> default('1');      //封面图片id
            $table -> tinyInteger('status') -> default('0');      //词条状态,0协作1开放2锁定3 关闭
            $table -> Integer('manage_id') -> default('1');    //自管理员id
            $table -> Integer('creator_id');    //创建者id
            // $table -> Integer('cooid') -> nullable();      //有效的协作计划id
            $table -> Integer('lasteditor_id');      //最后编辑者
            $table -> Integer('stars') -> default('0');
            $table -> Integer('shares') -> default('0');
            $table -> Integer('edit_number') -> default('1');
            $table -> timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entries');
    }
}
