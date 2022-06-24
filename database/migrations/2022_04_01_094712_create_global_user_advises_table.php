<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalUserAdvisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_user_advises', function (Blueprint $table) {
            $table->id();
            $table -> bigInteger('user_id');//作者id
            $table -> tinyInteger('scope');//建议区域
            $table -> string('title');//标题
            $table -> text('content');//内容
            $table -> tinyInteger('status')->default(0);//状态
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
        Schema::dropIfExists('global_user_advises');
    }
}
