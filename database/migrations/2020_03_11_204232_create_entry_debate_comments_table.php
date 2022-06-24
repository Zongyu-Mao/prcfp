<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryDebateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_debate_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eid');
            $table->integer('debate_id');
            $table->string('title');
            $table->text('comment');
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id'); 
            $table->integer('up') -> default(0);; 
            $table->integer('down') -> default(0);; 
            $table->tinyInteger('type') -> default(0);//类型：0是无立场，1是支持正方2是支持辩方
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
        Schema::dropIfExists('entry_debate_comments');
    }
}
