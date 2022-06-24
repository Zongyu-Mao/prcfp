<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceMarkDisposeWaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_mark_dispose_ways', function (Blueprint $table) {
            $table->id();//对巡查标记内容的处理方式，如删除、强制删除等
            $table->string('title');
            $table->tinyInteger('sort');
            $table->tinyInteger('weight');
            $table->string('description');
            $table->integer('creator_id');
            $table->integer('editor_id');
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
        Schema::dropIfExists('surveillance_mark_dispose_ways');
    }
}
