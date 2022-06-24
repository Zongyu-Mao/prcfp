<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitteeDocumentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('committee_document_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('did');
            $table->string('title');
            $table->text('comment');
            $table->integer('pid') -> default(0);  //从属评论id
            $table->integer('author_id'); 
            $table->string('author',20); 
            $table->timestamp('createtime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('committee_document_comments');
    }
}
