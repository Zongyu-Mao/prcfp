<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePictureGiveLikeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picture_give_like_records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('picture_id');
            $table->bigInteger('user_id');
            $table->tinyInteger('stand');
            $table->timestamps(); //可以更改
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picture_give_like_records');
    }
}
