<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_events', function (Blueprint $table) {
            $table->id();
            $table->integer('did');//所属评审计划id
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
        Schema::dropIfExists('document_events');
    }
}
