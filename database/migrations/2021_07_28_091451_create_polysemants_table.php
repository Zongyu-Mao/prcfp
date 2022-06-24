<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolysemantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polysemants', function (Blueprint $table) {
            $table->id();//多义词
            $table -> string('eid');//本词条id
            $table -> Integer('poly_id');//共享标题的多义词id
            $table -> Integer('creator_id');//创建者
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
        Schema::dropIfExists('polysemants');
    }
}
