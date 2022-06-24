<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_dynamics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eid');
            $table->string('entryTitle');
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
        Schema::dropIfExists('entry_dynamics');
    }
}
