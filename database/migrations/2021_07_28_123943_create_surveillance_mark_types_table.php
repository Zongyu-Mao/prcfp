<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceMarkTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_mark_types', function (Blueprint $table) {
            $table->id();//巡查标记种类，由于跟举报类型有差别，因此单独使用标记专用类型表格，参考medals勋章对比
            $table->string('title');
            $table->tinyInteger('sort')->default(1);
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
        Schema::dropIfExists('surveillance_mark_types');
    }
}
