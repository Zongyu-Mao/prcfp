<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleElectReactRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_elect_react_records', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('elect_id');
            $table->tinyInteger('stand');//1同意2反对,好像没有立场有哪里不对劲的样子。
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
        Schema::dropIfExists('role_elect_react_records');
    }
}
