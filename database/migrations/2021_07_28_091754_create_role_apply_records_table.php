<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleApplyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_apply_records', function (Blueprint $table) {
            $table->id();
            $table -> Integer('user_id');//申请者
            $table -> Integer('role_id');//申请角色id
            $table -> tinyInteger('status');//状态
            $table -> string('remark');//说明
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
        Schema::dropIfExists('role_apply_records');
    }
}
