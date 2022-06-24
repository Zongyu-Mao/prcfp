<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceArticleRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_article_records', function (Blueprint $table) {
            $table->id();
            $table -> Integer('user_id');//巡查者
            $table -> Integer('sid');//巡查对象id
            $table -> tinyInteger('editor_id');//修改的用户
            $table -> tinyInteger('status');// 其实是level 巡查级别，但是为了防止有其他需要，此处status
            $table -> tinyInteger('stand');//巡查结果1通过2不通过，基本巡查是为了创建和升级，因此只需要通过或不通过，不需要协作组反馈
            $table -> string('remark');//巡查记录
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
        Schema::dropIfExists('surveillance_article_records');
    }
}
