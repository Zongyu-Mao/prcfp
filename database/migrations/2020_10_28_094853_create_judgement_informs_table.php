<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgementInformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judgement_informs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('author_id')->default(0);
            $table->integer('object_user_id');
            $table->string('title');
            $table->tinyInteger('weight');
            $table->text('content');
            $table->string('url');
            $table->string('remark');
            $table->tinyInteger('scope');
            $table->integer('ground_id');
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
        Schema::dropIfExists('judgement_informs');
    }
}
