<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendPrivateLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friend_private_letters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('from_id');
            $table->string('from_username','20');
            $table->integer('to_id');
            $table->string('to_username','20');
            $table->string('title');
            $table->text('content');
            $table->integer('pid')->default('0');
            $table->tinyInteger('status')->default('0');
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
        Schema::dropIfExists('friend_private_letters');
    }
}
