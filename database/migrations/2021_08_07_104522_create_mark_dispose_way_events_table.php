<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkDisposeWayEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_dispose_way_events', function (Blueprint $table) {
            $table->id();
            $table->integer('wid');
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
        Schema::dropIfExists('mark_dispose_way_events');
    }
}
