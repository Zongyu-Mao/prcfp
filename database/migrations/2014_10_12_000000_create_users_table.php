<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table -> string('avatar') -> default(1);
            $table -> string('signature') -> default('文以登峰为极，章到至正是参'); //自我介绍
            $table -> string('password');
            $table -> Integer('specialty') -> default(0);                    //主专业
            $table -> tinyInteger('level') -> default(0);                    //用户等级，默认是0，认证后为1级
            $table -> tinyInteger('gold') -> default(0);                    //用户金币，默认是0，认证后为1
            $table -> tinyInteger('silver') -> default(0);                    //用户银币，默认是0，认证后为2
            $table -> tinyInteger('copper') -> default(0);                    //用户铜币，默认是0，认证后为5
            $table -> string('phone') -> nullable();    //电话
            $table -> tinyInteger('gender') -> default(1);//123
            $table -> tinyInteger('status') -> default(0);//默认是0，认证后是1
            $table -> Integer('role_id');       //角色id
            $table -> Integer('exp_value') -> default('0');     //经验值
            $table -> Integer('grow_value') -> default('0');    //成长值
            $table -> date('birthday') -> nullable();      //生日
            $table -> string('location',20) -> nullable();  //所在地
            $table -> string('regip',20) -> nullable();       //注册ip
            $table -> string('lastip',20) -> nullable();      //最后登录ip
            $table -> string('truename',20) -> nullable();//真实名字
            $table -> string('qq',20) -> nullable();//qq
            $table -> string('wechat',20) -> nullable();//微信
            $table -> string('msn',100) -> nullable();//msn
            $table -> date('lasttime') -> nullable();      //最后登录时间
            $table -> bigInteger('committee_id') -> default(0);     // 管理组
            $table -> bigInteger('gid') -> default(0);     // 主组织
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
