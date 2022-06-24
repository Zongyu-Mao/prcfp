<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupDocEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_doc_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('did');//所属组织文档id
            $table->integer('user_id');//行为人id
            $table->string('username');//行为人名称
            $table->text('content');//行为事件描述
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
        Schema::dropIfExists('group_doc_events');
    }
}
