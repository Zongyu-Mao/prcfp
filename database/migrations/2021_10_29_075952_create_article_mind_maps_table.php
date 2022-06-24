<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleMindMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_mind_maps', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pid')->default(0);
            $table->bigInteger('oid');//object_id
            $table->bigInteger('bid');
            $table->string('title');
            $table->tinyInteger('type');
            $table->bigInteger('creator_id');
            $table->bigInteger('editor_id')->default(0);
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
        Schema::dropIfExists('article_mind_maps');
    }
}
