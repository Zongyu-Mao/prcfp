<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->default(0);
            $table->integer('tcid');
            $table->integer('object_user_id');
            $table->string('title');
            $table->tinyInteger('weight');
            $table->text('content');
            $table->string('url');
            $table->string('remark');
            $table->tinyInteger('scope');
            $table->tinyInteger('belong');
            $table->integer('ground');
            $table->tinyInteger('status');
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
        Schema::dropIfExists('informs');
    }
}
