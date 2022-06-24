<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleDebateStarRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_debate_star_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('debate_id');//所属辩论id
            $table->integer('user_id');//行为人id
            $table->string('username');//行为人名称
            $table->tinyInteger('star'); //立场，0红1黑
            $table->tinyInteger('object'); //对象,0攻方1辩方2裁判
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
        Schema::dropIfExists('article_debate_star_records');
    }
}
