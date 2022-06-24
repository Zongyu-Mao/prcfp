<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_rights', function (Blueprint $table) {
            $table->id();
            $table -> string('right');//权限
            $table -> Integer('role_id');//需要角色，此处不使用id
            $table -> Integer('sort');
            $table -> string('introduction');
            $table -> bigInteger('creator_id');
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
        Schema::dropIfExists('role_rights');
    }
}
