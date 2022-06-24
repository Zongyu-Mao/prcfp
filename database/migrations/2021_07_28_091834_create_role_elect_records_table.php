<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleElectRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_elect_records', function (Blueprint $table) {
            $table->id();
            $table -> Integer('user_id');//推举者id
            $table -> Integer('elect_id');//被推举者id
            $table -> Integer('role_id');//推举角色id
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
        Schema::dropIfExists('role_elect_records');
    }
}
