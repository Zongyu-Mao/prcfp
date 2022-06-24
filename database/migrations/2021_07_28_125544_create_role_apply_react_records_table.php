<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleApplyReactRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_apply_react_records', function (Blueprint $table) {
            $table->id();//对于申请的反馈，只有同意选项，没有反对选项
            $table->integer('user_id');
            $table->integer('apply_id');
            $table->tinyInteger('stand');
            $table->string('remark');
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
        Schema::dropIfExists('role_apply_react_records');
    }
}
