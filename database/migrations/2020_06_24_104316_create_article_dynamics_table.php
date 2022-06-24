<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleDynamicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_dynamics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aid');
            $table->string('articleTitle');
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
        Schema::dropIfExists('article_dynamics');
    }
}
