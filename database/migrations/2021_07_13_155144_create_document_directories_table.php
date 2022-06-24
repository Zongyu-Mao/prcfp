<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentDirectoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_directories', function (Blueprint $table) {
            $table->id();
            $table->string('classname');//分类名
            $table->integer('pid');//父分类
            $table->tinyInteger('level');//层级，两级
            $table->integer('creator_id');//创建者id
            $table->integer('revisor_id')->nullable();//更新者id
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
        Schema::dropIfExists('document_directories');
    }
}
