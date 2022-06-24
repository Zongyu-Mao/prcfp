<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_dynamics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('username',20);
            $table->string('behavior');
            $table->string('objectName');
            $table->text('objectURL');
            $table->string('fromName');
            $table->text('fromURL');
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
        Schema::dropIfExists('user_dynamics');
    }
}
