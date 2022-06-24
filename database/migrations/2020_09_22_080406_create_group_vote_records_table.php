<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupVoteRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_vote_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('vote_id');
            $table->integer('user_id');
            $table->string('username','20');
            $table->tinyInteger('standpoint'); //立场，1同意2反对3中立
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
        Schema::dropIfExists('group_vote_records');
    }
}
