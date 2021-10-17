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
            $table->increments('id');
            $table->string('username', 50);
            $table->string('password');
            $table->integer('user_group_id')->default(4);
            $table->integer('user_type_id')->default(1);
            $table->mediumText('name');            
            $table->string('email', 100)->nullable();
            $table->string('mobile_no', 20)->nullable();
            $table->integer('ordering')->default(9999);            
            $table->string('status', 11)->default('Active')->comment('Active, In-Active, Deleted');            
            $table->longText('infos')->nullable();
            $table->rememberToken();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
