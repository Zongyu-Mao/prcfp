<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassificationModifyRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classification_modify_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('class_id');//分类id
            $table->integer('user_id');//行为人id
            $table->string('username');//行为人名称
            $table->text('content');//行为事件描述
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
        Schema::dropIfExists('classification_modify_records');
    }
}
