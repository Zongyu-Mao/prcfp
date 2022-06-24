<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamCooperationEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exam_cooperation_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cooperation_id');//所属协作计划id
            $table->integer('user_id');//行为人id
            $table->string('username','20');//行为人id
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
        Schema::dropIfExists('exam_cooperation_events');
    }
}
