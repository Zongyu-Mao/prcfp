<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEditorTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('editor_times', function (Blueprint $table) {
            // 这里的统计范围采用inform里的统计方法，范围计数同inform
            $table->id();
            $table -> Integer('user_id');
            $table -> tinyInteger('type');
            $table -> tinyInteger('scope');
            $table -> Integer('editor_id');
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
        Schema::dropIfExists('editor_times');
    }
}
