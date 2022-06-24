<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryReviewDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_review_discussions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rid');
            $table->string('title');
            $table->text('comment');
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id');
            $table->string('author',20);
            $table->tinyInteger('standpoint') -> default(2);//评论立场，1是支持，2是普通，3是中立
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
        Schema::dropIfExists('entry_review_discussions');
    }
}
