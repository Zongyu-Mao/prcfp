<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_dynamics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gid');
            $table->string('gTitle');
            $table->string('behavior');
            $table->string('objectName');
            $table->text('objectURL');
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
        Schema::dropIfExists('group_dynamics');
    }
}
