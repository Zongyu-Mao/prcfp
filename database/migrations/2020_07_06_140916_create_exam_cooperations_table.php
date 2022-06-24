<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamCooperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_cooperations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id');  //所属著作id
            $table->integer('cid');  //词条所属底层分类id
            $table->string('title');  //词条的名称（与版本号一起当做协作计划的名称）
            $table->tinyInteger('target');  //所属词条id
            $table->tinyInteger('timelimit');  //计划周期
            $table->timestamp('deadline');  //到期时间
            $table->tinyInteger('ifseeking');  //对他人合作的态度
            $table->text('assign') -> nullable();  //协作内容分配
            $table->tinyInteger('version') -> default(1);  //版本
            $table->integer('creator_id');  //创建者id
            $table->string('creator','20');  //创建者
            $table->integer('manage_id');  //组长（自管理员）id
            $table->string('manager','20');  //创建者
            $table->tinyInteger('status') -> default('0');  //计划状态，0在过程中，1未达标，2达标，3放弃
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
        Schema::dropIfExists('exam_cooperations');
    }
}
