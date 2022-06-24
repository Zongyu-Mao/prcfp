<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntryContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entry_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eid'); //所属词条id
            $table->text('content');  //对应的词条正文内容
            $table->integer('edittor_id'); //创建者id
            $table->tinyInteger('sort'); //创建者id
            $table->string('ip',15); //作者ip
            $table->tinyInteger('big')->default(0);  //是否为大改动,创建时系统带入，其余用户选择
            $table->string('reason');  //改动的原因，创建时系统带入，其余用户输入
            $table->timestamps();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entry_contents');
    }
}
