<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveillanceWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surveillance_warnings', function (Blueprint $table) {
            $table->id();
            $table -> Integer('user_id');//巡查者
            $table -> Integer('sid');//巡查警示对象id
            $table -> string('warning');//警示，警示不需要协作组反馈，但是需要提出者主动改变status
            $table -> tinyInteger('status')->default(0);
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
        Schema::dropIfExists('surveillance_warnings');
    }
}
