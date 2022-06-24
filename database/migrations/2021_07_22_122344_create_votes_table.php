<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('affiliation')->default(2);//归属1系统2用户
            $table->tinyInteger('type');//是否多选
            $table->tinyInteger('amount');//选项
            $table->tinyInteger('choice_limit')->default(1);//多选值
            $table->timestamp('deadline');
            $table->integer('initiate_id');
            $table->string('title');
            $table->text('content');
            $table->tinyInteger('status')->default(1);
            $table->string('remark');
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
        Schema::dropIfExists('votes');
    }
}
