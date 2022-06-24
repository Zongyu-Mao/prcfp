<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalUserAdviseCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_user_advise_comments', function (Blueprint $table) {
            $table->id();
            $table -> bigInteger('advise_id');//advise id
            $table -> bigInteger('user_id');//作者id
            $table -> string('content');//内容
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
        Schema::dropIfExists('global_user_advise_comments');
    }
}
