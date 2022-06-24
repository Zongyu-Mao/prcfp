<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
            $table->id();
            $table -> date('showtime');
            $table -> bigInteger('eid')->default(0);
            $table -> Integer('cid');
            $table -> string('title');
            $table -> text('introduction');
            $table -> string('url');
            $table -> tinyInteger('status')->default(0);
            $table -> tinyInteger('ups')->default(0);
            $table -> tinyInteger('downs')->default(0);
            $table -> bigInteger('creator_id');
            $table -> string('creator');
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
        Schema::dropIfExists('pictures');
    }
}
