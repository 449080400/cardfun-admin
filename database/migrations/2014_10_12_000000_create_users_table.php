<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('email')->nullable()->index();
            $table->string('name')->nullable()->index();
            $table->string('login_token')->nullable();
            $table->string('remember_token')->nullable();
            $table->enum('is_banned', ['yes',  'no'])->default('no')->index();
            $table->string('city')->nullable();
            $table->string('introduction')->nullable();
            $table->integer('notification_count')->default(0);
            $table->string('real_name')->nullable();
            $table->string('avatar');
            $table->string('wechat_openid')->nullable()->index();
            $table->string('wechat_unionid')->nullable()->index();
            $table->boolean('verified')->default(false)->index();
            $table->string('verification_token')->nullable();
            $table->enum('email_notify_enabled', ['yes',  'no'])->default('yes')->index();
            $table->string('register_source')->index();
            $table->timestamp('last_actived_at')->nullable();
            $table->softDeletes();
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
