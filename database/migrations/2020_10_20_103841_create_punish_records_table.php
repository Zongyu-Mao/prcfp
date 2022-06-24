<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punish_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medal_id');
            $table->integer('handle_id')->default(0);
            $table->integer('punish_id');
            $table->string('url');
            $table->tinyInteger('type');
            $table->timestamp('endtime');
            $table->timestamp('createtime');
            $table->tinyInteger('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('punish_records');
    }
}
