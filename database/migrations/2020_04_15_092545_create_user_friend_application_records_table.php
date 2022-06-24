<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserFriendApplicationRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_friend_application_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('username','20');
            $table->integer('application_id');
            $table->string('application_username','20');
            $table->string('title');
            $table->text('content');
            $table->tinyInteger('applyResult');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_friend_application_records');
    }
}
