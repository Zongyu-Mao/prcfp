<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupMarkRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_mark_records', function (Blueprint $table) {
            $table->id();
            $table -> Integer('mark_id');//巡视标记id
            $table -> Integer('user_id');//处理者
            $table -> tinyInteger('stand');//处理的立场，1同意2不同意
            $table -> string('remark');//记录
            $table->timestamp('createtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_mark_records');
    }
}
